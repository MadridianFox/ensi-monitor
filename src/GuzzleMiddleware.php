<?php

namespace Ensi\Monitor;

use Ensi\Monitor\Events\GuzzleRequestHandled;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleMiddleware
{
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, $options) use ($handler) {
            $startTime = microtime(true);
            return $handler($request, $options)->then(function (ResponseInterface $response) use ($request, $startTime) {
                $duration = microtime(true) - $startTime;
                GuzzleRequestHandled::dispatch($request, $response, $duration);

                return $response;
            });
        };
    }
}