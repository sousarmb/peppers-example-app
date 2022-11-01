<?php

namespace App\Events\Handlers;

use DateInterval;
use DateTime;
use Peppers\AppEvent;
use Peppers\Contracts\EventHandler;
use RuntimeException;
use Settings;

class LogRequestHandler implements EventHandler {

    /**
     * 
     * @param AppEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(AppEvent $event): bool {
        $logFile = sprintf('%s%s-%s',
                Settings::get('LOGS_DIR'),
                (new DateTime('now'))->format('Ymd'),
                Settings::get('KERNEL_LOG_FILE')
        );
        $fh = fopen($logFile, 'a');
        if (!$fh) {
            // cannot open log file, something is wrong
            throw new RuntimeException('Could not get handle on log file on ' . __CLASS__);
        }

        $eventData = $event->getData();
        array_walk($eventData, function (&$entry) {
            // 1st entry is already a string, from then on represent the time 
            // difference to the beginning of the request
            if ($entry[0] instanceof DateInterval) {
                $entry[0] = $entry[0]->format('%f|%s');
            }
        });
        return (bool) fwrite($fh, json_encode($eventData, JSON_FORCE_OBJECT) . PHP_EOL);
    }

}
