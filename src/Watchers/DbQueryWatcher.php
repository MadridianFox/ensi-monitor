<?php

namespace Ensi\Monitor\Watchers;

use Ensi\Monitor\AbstractWatcher;
use Illuminate\Database\Events\QueryExecuted;

class DbQueryWatcher extends AbstractWatcher
{
    protected function getTopicName(): string
    {
        return config('monitor.topics.db-query.name');
    }

    public function handle(QueryExecuted $event)
    {
        $this->send([
            'sql' => $event->sql,
            'time' => round($event->time / 1000, 3),
        ]);
    }
}