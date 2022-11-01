<?php

namespace App\Events\Handlers;

use Peppers\AppEvent;
use Peppers\Contracts\EventHandler;
use Peppers\ServiceLocator;
use Peppers\Contracts\RouteResolver;
use Settings;

class BuildRoutesCacheFileHandler implements EventHandler {

    /**
     * 
     * @param AppEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(AppEvent $event): bool {
        $lockFile = $event->getData()[0];
        ftruncate($lockFile, 0);
        $resolver = serialize(ServiceLocator::get(RouteResolver::class));
        fwrite($lockFile, $resolver);
        rename(
                Settings::get('TEMP_DIR') . Settings::get('ROUTES_CACHE_FILENAME') . '.lock',
                Settings::get('TEMP_DIR') . Settings::get('ROUTES_CACHE_FILENAME'),
                $lockFile
        );
        return fclose($lockFile);
    }

}
