<?php

namespace Ensi\Monitor\Watchers;

use Ensi\Monitor\AbstractWatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class HttpInWatcher extends AbstractWatcher
{
    protected function getTopicName(): string
    {
        return config('monitor.topics.http-in.name');
    }

    public function handle(RequestHandled $event)
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');
        $message = [
            'path' => str_replace($event->request->root(), '', $event->request->fullUrl()) ?: '/',
            'method' => $event->request->method(),
            'code' => $event->response->getStatusCode(),
            'duration' => $startTime ? round(microtime(true) - $startTime, 3) : null,
        ];
        if (config('monitor.topics.http-in.with-body')) {
            $message['req_headers'] = $this->renderHeaders($event->request);
            $message['req_body'] = $this->renderBody($event->request);
            $message['res_headers'] = $this->renderHeaders($event->response);
            $message['res_body'] = $this->renderBody($event->response);
        }

        $this->send($message);
    }

    protected function renderHeaders(Request|Response $request): string
    {
        $result = [];
        foreach ($request->headers->all() as $key => $values) {
            $result[$key] = join(',', $values);
        }
        return json_encode($result);
    }

    protected function renderBody(Request|Response $request)
    {
        $contentType = strtolower($request->headers->get('Content-Type') ?? '');

        if (Str::startsWith($contentType, ['application/json', 'text/plain'])) {
            return $request->getContent();
        }

        return null;
    }
}