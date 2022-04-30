<?php

namespace Ensi\Monitor\Logging;

use Ensi\LaravelPhpRdKafkaProducer\HighLevelProducer;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class MonologKafkaHandler extends AbstractProcessingHandler
{
    private HighLevelProducer $producer;

    public function __construct($level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->producer = new HighLevelProducer('logs', flushTimeout: 100, flushRetries: 1);
    }


    protected function write(array $record): void
    {
        try {
            $this->producer->sendOne((string)$record['formatted']);
        } catch (\Throwable $e) {
            // do nothing
        }
    }
}
