<?php

namespace Ensi\Monitor\Watchers;

use Ensi\LaravelInitialEventPropagation\InitialEventHolderFacade;
use Ensi\Monitor\AbstractWatcher;
use Ensi\Monitor\Core;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class HttpInWatcher extends AbstractWatcher
{
    protected function getTopicName(): string
    {
        return 'http-in';
    }

    public function handle(RequestHandled $event)
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');
        $this->send([
            'correlation_id' => InitialEventHolderFacade::getInitialEvent()->correlationId,
            'entrypoint' => Core::getTxnId(),
            'path' => str_replace($event->request->root(), '', $event->request->fullUrl()) ?: '/',
            'method' => $event->request->method(),
            'headers' => $this->renderRequestHeaders($event->request),
            'req_body' => $this->renderRequestBody($event->request),
            'code' => $event->response->getStatusCode(),
            'res_body' => $this->renderResponseBody($event->response),
            'duration' => $startTime ? round(microtime(true) - $startTime, 3) : null,
        ]);
    }

    protected function renderRequestHeaders(Request $request): array
    {
        $result = [];
        foreach ($request->headers->all() as $key => $values) {
            $result[$key] = join(',', $values);
        }
        return $result;
    }

    protected function renderRequestBody(Request $request): ?string
    {
        $contentType = strtolower($request->headers->get('Content-Type') ?? '');

        if (Str::startsWith($contentType, 'application/json')) {
            return json_decode($request->getContent());
        }

        if (Str::startsWith($contentType, 'text/plain')) {
            return $request->getContent();
        }

        return null;
    }

    protected function renderResponseBody(\Symfony\Component\HttpFoundation\Response $response)
    {
        $contentType = strtolower($response->headers->get('Content-Type') ?? '');

        if (Str::startsWith($contentType, 'application/json')) {
            return json_decode($response->getContent());
        }

        if (Str::startsWith($contentType, 'text/plain')) {
            return $response->getContent();
        }

        return null;
    }
}