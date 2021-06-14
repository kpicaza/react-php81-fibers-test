<?php

declare(strict_types=1);

use React\EventLoop\Factory;

require 'vendor/autoload.php';

$loop = Factory::create();

$server = new \React\Http\Server(
    $loop,
    new \React\Http\Middleware\StreamingRequestMiddleware(),
    new \React\Http\Middleware\LimitConcurrentRequestsMiddleware(100), // 100 concurrent buffering handlers
    new \React\Http\Middleware\RequestBodyBufferMiddleware(2 * 1024 * 1024), // 2 MiB per request
    new \React\Http\Middleware\RequestBodyParserMiddleware(),
    function (\Psr\Http\Message\ServerRequestInterface $request) use ($loop) {
    return new \React\Http\Message\Response(
        200,
        array(
            'Content-Type' => 'text/plain'
        ),
        "Hello World!\n"
    );
});

$server->on('error', function (Throwable $exception) {
    dump($exception);
});

$socket = new \React\Socket\Server('0.0.0.0:8080', $loop);
$server->listen($socket);

$loop->run();
