<?php

namespace Ensi\Monitor\Watchers;

use Ensi\Monitor\AbstractWatcher;
use Ensi\Monitor\Events\GuzzleRequestHandled;
use Illuminate\Support\Str;
use Psr\Http\Message\MessageInterface;

class HttpOutWatcher extends AbstractWatcher
{
    protected function getTopicName(): string
    {
        return config('monitor.topics.http-out.name');
    }

    public function handle(GuzzleRequestHandled $event)
    {
        $message = [
            'code' => $event->response->getStatusCode(),
            'duration' => round($event->duration, 3),
            'method' => $event->request->getMethod(),
            'host' => $event->request->getUri()->getHost(),
            'path' => $event->request->getUri()->getPath(),

        ];

        if (config('monitor.topics.http-out.with-body')) {
            $message['req_headers'] = $this->renderHeaders($event->request);
            $message['req_body'] = $this->renderBody($event->request);
            $message['res_headers'] = $this->renderHeaders($event->response);
            $message['res_body'] = $this->renderBody($event->response);
        }

        $this->send($message);
    }

    public function renderHeaders(MessageInterface $message): string
    {
        $result = [];
        foreach ($message->getHeaders() as $key => $values) {
            $result[$key] = join(',', $values);
        }

        return json_encode($result);
    }

    public function renderBody(MessageInterface $message)
    {
        $contentType = strtolower(current($message->getHeader('Content-Type') ?? []) ?? "");
        if (Str::startsWith(strtolower($contentType), ['application/json', 'text/plain'])) {
            return (string)$message->getBody();
        }

        return null;
    }
}