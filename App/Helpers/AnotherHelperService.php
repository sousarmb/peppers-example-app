<?php

namespace App\Helpers;

use App\Contracts\Helper;

class AnotherHelperService implements Helper {

    /**
     * 
     * @return string
     */
    public function getHelp(): string {
        return 'I help even more!';
    }

}
