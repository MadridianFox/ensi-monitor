<?php

namespace Ensi\Monitor;

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
        try {
            $this->producer->sendOne(json_encode($message));
        } catch (\Throwable $e) {
            // do nothing
        }
    }
}