<?php

namespace Ensi\Monitor;

use Ensi\LaravelInitialEventPropagation\InitialEventHolderFacade;
use Ensi\LaravelPhpRdKafkaProducer\HighLevelProducer;

abstract class AbstractWatcher
{
    protected HighLevelProducer $producer;

    protected abstract function getTopicName(): string;

    public function __construct()
    {
        $this->producer = new HighLevelProducer($this->getTopicName(), flushTimeout: 100, flushRetries: 1);
    }

    protected function send(array $message): void
    {
        $data = array_merge($this->getBaseFields(), $message);
        try {
            $this->producer->sendOne(json_encode($data));
        } catch (\Throwable $e) {
            // do nothing
        }
    }

    protected function getBaseFields(): array
    {
        return [
            'app' => strtolower(str_replace(" ", "-", config('app.name'))),
            'tenant' => config('monitor.tenant'),
            'instance' => config('monitor.instance'),
            'correlation_id' => InitialEventHolderFacade::getInitialEvent()->correlationId,
            'entrypoint' => Core::getTxnId(),
        ];
    }
}