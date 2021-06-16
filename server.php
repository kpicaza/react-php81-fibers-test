<?php

declare(strict_types=1);

use React\EventLoop\Factory;

require 'vendor/autoload.php';

// Change to `true` to try multi-thread
$multiThreads = true;

$loop = Factory::create();
$socket = new \React\Socket\Server('0.0.0.0:8080', $loop, [
    'tcp' => [
        'reuse_port' => true
    ]
]);

$serverInstance = static function () use ($loop, $socket) {

    $server = new \React\Http\Server(
        $loop,
        new \React\Http\Middleware\StreamingRequestMiddleware(),
        new \React\Http\Middleware\LimitConcurrentRequestsMiddleware(512), // 100 concurrent buffering handlers
        new \React\Http\Middleware\RequestBodyBufferMiddleware(1024), // 2 MiB per request
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

    $server->listen($socket);
};

if ($multiThreads) {
    \Antidot\React\Child::fork(8, $serverInstance);
} else {
    $serverInstance();
}


$loop->run();
