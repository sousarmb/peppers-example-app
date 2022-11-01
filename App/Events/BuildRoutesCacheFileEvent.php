<?php

namespace App\Events;

use Peppers\AppEvent;
use Settings;

class BuildRoutesCacheFileEvent extends AppEvent {

    /**
     * 
     * @return self|null
     */
    public function dispatch(): ?self {
        $lockFile = Settings::get('TEMP_DIR') . Settings::get('ROUTES_CACHE_FILENAME') . '.lock';
        if (is_readable($lockFile)) {
            // some other process is handling this already
            return null;
        }
        // block new cache file creation by other processes
        $this->data = [fopen($lockFile, 'x')];
        return parent::dispatch();
    }

}
