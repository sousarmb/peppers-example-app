<?php

namespace App\Events;

use Peppers\AppEvent;

class LogRequestEvent extends AppEvent {

    /**
     * 
     * @return self|null
     */
    public function dispatch(): ?self {
        return parent::dispatch();
    }

}
