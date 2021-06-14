<?php

declare(strict_types=1);

use React\EventLoop\Factory;
use Trowski\ReactFiber\FiberLoop;

require 'vendor/autoload.php';

ini_set('memory_limit', '-1');

$loop = new FiberLoop(Factory::create());

$server = new \React\Http\Server(
    $loop,
    new \React\Http\Middleware\StreamingRequestMiddleware(),
    new \React\Http\Middleware\LimitConcurrentRequestsMiddleware(512), // 100 concurrent buffering handlers
    new \React\Http\Middleware\RequestBodyBufferMiddleware(1024), // 2 MiB per request
    new \React\Http\Middleware\RequestBodyParserMiddleware(),
    function (\Psr\Http\Message\ServerRequestInterface $request) use ($loop) {
        return $loop->async(function () {
            return new \React\Http\Message\Response(
                200,
                array(
                    'Content-Type' => 'text/plain'
                ),
                "Hello World!\n"
            );
        });
    });

$server->on('error', function (Throwable $exception) {
    dump($exception);
});

$socket = new \React\Socket\Server('0.0.0.0:8080', $loop);
$server->listen($socket);

$loop->run();
