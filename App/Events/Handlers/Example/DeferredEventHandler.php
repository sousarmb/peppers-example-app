<?php

namespace App\Events\Handlers\Example;

use Peppers\AppEvent;
use Peppers\Contracts\EventHandler;

class DeferredEventHandler implements EventHandler {

    /**
     * 
     * @param AppEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(AppEvent $event): bool {
        sleep(2);
        ob_start();
        echo sprintf('<!-- %s -->', $event->getData()[0]);
        echo sprintf('<!-- Server time: %s -->', date('Y-m-d H:i:s'));
        return ob_end_flush();
    }

}
