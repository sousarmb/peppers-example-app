<?php

namespace App\Events\Example;

use Peppers\AppEvent;

class BlockingEvent extends AppEvent {

    /**
     * 
     * @return self|null
     */
    public function dispatch(): ?self {
        return parent::dispatch();
    }

}
