<?php

namespace App\Controllers\Example;

use Peppers\Helpers\ViewDataStore;
use Peppers\Renderer\HtmlView;
use Peppers\Response;

class ViewComposition {

    /**
     * 
     * @return Response
     */
    public function viewComposition(): Response {
        $viewData = new ViewDataStore([
            'exampleName' => 'View Composition'
        ]);
        $view = new HtmlView(
                'examples.view.composition.content',
                $viewData
        );
        return (new Response())->html($view);
    }

}
