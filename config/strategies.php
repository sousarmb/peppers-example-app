<?php

use Peppers\Strategy;
use Peppers\Strategy\Boot;
use Peppers\Strategy\Response;

return [
    'boot' => [
        Boot\ServiceLocator::class,
        Boot\SessionStart::class
    ],
    'requestResponse' => [
        Strategy\RouteToHandler::class,
        Response\Html::class,
        Response\Json::class,
        Response\Redirect::class,
        Response\NoBody::class,
        Response\File::class,
        Response\PlainText::class,
        Response\Xml::class
    ],
    'exceptionHandling' => [
        Strategy\ResolveException::class,
        Response\Html::class,
        Response\Json::class,
        Response\PlainText::class,
        Response\Xml::class
    ],
    'shutdown' => [
        Strategy\ShutdownServices::class,
    ]
];
