<?php

namespace App\Controllers\Example;

use Peppers\Helpers\Http\Request\PathParameter;
use Peppers\Helpers\Http\Redirect;
use Peppers\Helpers\ViewDataStore;
use Peppers\Renderer\HtmlView;
use Peppers\Response;

class Routing {

    /**
     * 
     * @param PathParameter $param1
     * @param PathParameter $param2
     * @return Response
     */
    public function readFromRequest(
            PathParameter $param1,
            PathParameter $param2
    ): Response {
        $viewData = new ViewDataStore([
            'exampleName' => 'Routing',
            'param1' => $param1,
            'param2' => $param2
        ]);
        $view = new HtmlView(
                'examples.routing.parameters.content',
                $viewData
        );
        return (new Response())->html($view);
    }

    /**
     * 
     * @return Response
     */
    public function redirect(): Response {
        return (new Response())->redirect(new Redirect('/abc'));
    }

}
