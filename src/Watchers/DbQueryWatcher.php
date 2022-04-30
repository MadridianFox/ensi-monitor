<?php

namespace Ensi\Monitor\Watchers;

use Ensi\LaravelInitialEventPropagation\InitialEventHolderFacade;
use Ensi\Monitor\AbstractWatcher;
use Ensi\Monitor\Core;
use Illuminate\Database\Events\QueryExecuted;

class DbQueryWatcher extends AbstractWatcher
{
    protected function getTopicName(): string
    {
        return 'db-queries';
    }

    public function handle(QueryExecuted $event)
    {
        $this->send([
            'correlation_id' => InitialEventHolderFacade::getInitialEvent()->correlationId,
            'entrypoint' => Core::getTxnId(),
            'sql' => $event->sql,
            'time' => round($event->time / 1000, 3),
        ]);
    }
}