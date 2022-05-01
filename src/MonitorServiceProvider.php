<?php

namespace Ensi\Monitor;

use Ensi\Monitor\Events\GuzzleRequestHandled;
use Ensi\Monitor\Watchers\DbQueryWatcher;
use Ensi\Monitor\Watchers\HttpInWatcher;
use Ensi\Monitor\Watchers\HttpOutWatcher;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\ServiceProvider;

class MonitorServiceProvider extends ServiceProvider
{
    public function register()
    {
        //$this->app->make('config')->set('logging.channels.kafka', [
        //    'driver' => 'monolog',
        //    'level' => env('LOG_LEVEL', 'debug'),
        //    'handler' => MonologKafkaHandler::class,
        //    'formatter' => \Monolog\Formatter\JsonFormatter::class,
        //]);

        $this->mergeConfigFrom(__DIR__.'/../config/monitor.php', 'monitor');

        if (config('monitor.enabled')) {
            if (config('monitor.topics.http-in.enabled')) {
                $this->app->singleton(HttpInWatcher::class);
                $this->app['events']->listen(RequestHandled::class, HttpInWatcher::class);
            }

            if (config('monitor.topics.http-out.enabled')) {
                $this->app->singleton(HttpOutWatcher::class);
                $this->app['events']->listen(GuzzleRequestHandled::class, HttpOutWatcher::class);
            }

            if (config('monitor.topics.db-query.enabled')) {
                $this->app->singleton(DbQueryWatcher::class);
                $this->app['events']->listen(QueryExecuted::class, DbQueryWatcher::class);
            }
        }
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/monitor.php' => config_path('monitor.php'),
            ], 'config');
        }
    }
}
