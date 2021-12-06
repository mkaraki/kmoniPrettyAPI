<?php
require_once 'vendor/autoload.php';

use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::get('/v1/info', function () {
    $is_manual = false;
    $path = time();
    require __DIR__ . '/v1/info.php';
});

SimpleRouter::get('/v1/info/{time}', function ($time) {
    $is_manual = true;
    $path = $time;
    require __DIR__ . '/v1/info.php';
});

SimpleRouter::start();
