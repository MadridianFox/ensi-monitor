<?php

namespace Ensi\Monitor\Watchers;

use Ensi\LaravelInitialEventPropagation\InitialEventHolderFacade;
use Ensi\Monitor\AbstractWatcher;
use Ensi\Monitor\Core;
use Ensi\Monitor\Events\GuzzleRequestHandled;
use Illuminate\Support\Str;
use Psr\Http\Message\MessageInterface;

class HttpOutWatcher extends AbstractWatcher
{
    protected function getTopicName(): string
    {
        return 'http-out';
    }

    public function handle(GuzzleRequestHandled $event)
    {
        $this->send([
            'correlation_id' => InitialEventHolderFacade::getInitialEvent()->correlationId,
            'entrypoint' => Core::getTxnId(),
            'code' => $event->response->getStatusCode(),
            'duration' => round($event->duration, 3),
            'method' => $event->request->getMethod(),
            'path' => $event->request->getUri(),
            'req_headers' => $this->renderHeaders($event->request),
            'req_body' => $this->renderBody($event->request),
            'res_headers' => $this->renderHeaders($event->response),
            'res_body' => $this->renderBody($event->response),
        ]);
    }

    public function renderHeaders(MessageInterface $message): array
    {
        $result = [];
        foreach ($message->getHeaders() as $key => $values) {
            $result[$key] = join(',', $values);
        }

        return $result;
    }

    public function renderBody(MessageInterface $message)
    {
        $contentType = strtolower(current($message->getHeader('Content-Type') ?? []) ?? "");
        if (Str::startsWith(strtolower($contentType), 'application/json')) {
            return json_decode((string)$message->getBody());
        }
        if (Str::startsWith(strtolower($contentType), 'text/plain')) {
            return (string)$message->getBody();
        }

        return null;
    }
}