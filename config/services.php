<?php

// use Peppers\Helpers\Http\Request\BodyParameter;
// use Peppers\Helpers\Http\Request\QueryParameter;
// use Peppers\Helpers\Http\Request\PathParameter;
use Peppers\Helpers\Service\BoundTo;
use Peppers\Helpers\Service\Implementation as Imp;
use Peppers\Services;
use Peppers\Contracts;

return [
    Imp::abstract(Contracts\RouteResolver::class)
        ->setProvider(Peppers\Strategy\Boot\RouteResolver::class)
        ->setIsSingleton(true),
    Imp::concrete(Services\RequestBody::class)
        ->setIsLazyLoad(true)
        ->setIsSingleton(true),
    Imp::abstract(Contracts\EventStore::class)
        ->setProvider(\Peppers\Strategy\Boot\EventStore::class)
        ->setIsSingleton(true),
    Imp::abstract(Contracts\CredentialStore::class)
        ->setProvider(Peppers\Strategy\Boot\CredentialStore::class)
        ->setIsSingleton(true),
    Imp::abstract(Contracts\ConnectionManager::class)
        ->setProvider(Peppers\Strategy\Boot\ConnectionManager::class)
        ->setIsSingleton(true),
    Imp::abstract(App\Contracts\Helper::class, [App\Controllers\Example\DependencyInjection::class])
        ->setProvider(function (BoundTo $caller) {
            if ($caller->getName() == App\Controllers\Example\DependencyInjection::class) {
                // one of the classes in the bindings array
                return new App\Helpers\HelperService();
            }
            // other classes get this implementation
            return new App\Helpers\AnotherHelperService();
        })
];
