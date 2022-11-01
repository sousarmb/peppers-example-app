<?php

namespace App\Events\Handlers\Example;

use Peppers\AppEvent;
use Peppers\Contracts\EventHandler;
use Peppers\Kernel;

class BlockingEventHandler implements EventHandler {

    /**
     * 
     * @param AppEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(AppEvent $event): bool {
        sleep(2);
        /* because output buffer is cleaned before rendering any output no 
         * content can output until then so just block for 2 seconds and write 
         * a message to the log file */
        Kernel::log(sprintf('<!-- %s -->', $event->getData()[0]));
        return true;
    }

}
