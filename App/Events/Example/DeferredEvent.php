<?php

namespace App\Events\Example;

use Peppers\AppEvent;

class DeferredEvent extends AppEvent {

    /**
     * 
     * @return self|null
     */
    public function dispatch(): ?self {
        return parent::dispatch();
    }

}
