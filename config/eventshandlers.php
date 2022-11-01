<?php

use App\Events as E;
use App\Events\Handlers as H;

return [
    E\BuildRoutesCacheFileEvent::class => [
        H\BuildRoutesCacheFileHandler::class,
    ],
    E\LogRequestEvent::class => [
        H\LogRequestHandler::class,
    ],
    E\Example\BlockingEvent::class => [
        H\Example\BlockingEventHandler::class
    ],
    E\Example\DeferredEvent::class => [
        H\Example\DeferredEventHandler::class
    ]
];
