<?php

namespace App\Helpers;

use App\Contracts\Helper;

class HelperService implements Helper {

    /**
     * 
     * @return string
     */
    public function getHelp(): string {
        return 'I help a lot';
    }

}
