<?php

namespace App\Controllers\Example;

use App\Contracts\Helper;
use Peppers\Helpers\ResponseSent;
use Peppers\Helpers\ViewDataStore;
use Peppers\Renderer\HtmlView;
use Peppers\Response;

class DependencyInjection {

    public function inject(Helper $service): Response|ResponseSent {
        $viewDataStore = new ViewDataStore();
        $viewDataStore->exampleName = 'Dependency injection';
        $viewDataStore->serviceOutput = $service->getHelp();
        $formView = new HtmlView(
                'examples.dependency-injection.helper-service.content',
                $viewDataStore
        );

        return (new Response())->html($formView);
    }

}
