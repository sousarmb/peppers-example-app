<?php

namespace App\Controllers\Peppers;

use Peppers\Contracts\DefaultMethod;
use Peppers\Helpers\ViewDataStore;
use Peppers\Renderer\HtmlView;
use Peppers\Response;
use Settings;

class DefaultController implements DefaultMethod {

    /**
     * 
     * @return mixed
     */
    public function default(): Response {
        /* it's not mandatory to return HTML, do your own logic and decide 
         * what's best. For demonstration sake 404 with HTML response is 
         * returned */
        $viewData = new ViewDataStore([
            'title' => 'Peppers Default Controller',
            'use' => 'This is the default controller, used when a route does not exist.',
            'description' => 'It\'s enabled by default in Settings class, index.php file. If disabled only a HTTP 404 response is returned.'
        ]);
        // once dispatched ViewDataStore is immutable
        $view = new HtmlView(
                'Peppers.DefaultController.defaultSection',
                $viewData
        );
        return (new Response())->html($view)->setStatusCode(404);
    }

}
