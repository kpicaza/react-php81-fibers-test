<?php

declare(strict_types=1);

use React\EventLoop\Factory;
use Trowski\ReactFiber\FiberLoop;

require 'vendor/autoload.php';

ini_set('memory_limit', '-1');

// Change to `true` to try multi-thread
$multiThreads = true;

$loop = new FiberLoop(Factory::create());
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
        new \React\Http\Middleware\RequestBodyBufferMiddleware(2 * 1024), // 2 MiB per request
        new \React\Http\Middleware\RequestBodyParserMiddleware(),
        function (\Psr\Http\Message\ServerRequestInterface $request) use ($loop) {
            try {
                return $loop->async(static function () use ($loop) {
                    $message = $loop->await(\React\Promise\resolve('Hola mundo'));

                    return new \React\Http\Message\Response(
                        200,
                        array(
                            'Content-Type' => 'application/json'
                        ),
                        json_encode([
                            'message' => $message,
                        ])
                    );
                });
            } catch (Throwable $exception) {
                dump($exception);
            }
        });

    $server->listen($socket);
};

if ($multiThreads) {
    \Antidot\React\Child::fork(8, $serverInstance);
} else {
    $serverInstance();
}


$loop->run();
