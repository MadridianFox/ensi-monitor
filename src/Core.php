<?php

namespace Ensi\Monitor;

use Illuminate\Support\Facades\Route;

class Core
{
    public static function getTxnId(): string
    {
        $laravelRoute = Route::current();
        if ($laravelRoute) {
            return $laravelRoute->uri;
        } else {
            if (isset($_SERVER['REQUEST_METHOD']) && isset($_SERVER['REQUEST_URI'])) {
                return $_SERVER['REQUEST_METHOD'] . ' ' . self::normalizeHttpUri($_SERVER['REQUEST_URI']);
            } else {
                return 'undefined_web';
            }
        }
    }
}