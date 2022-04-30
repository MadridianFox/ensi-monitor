<?php

namespace Ensi\Monitor\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleRequestHandled
{
    use Dispatchable;

    public function __construct(
        public RequestInterface $request,
        public ResponseInterface $response,
        public float $duration
    ) {
    }
}