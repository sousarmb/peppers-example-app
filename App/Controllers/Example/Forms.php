<?php

namespace App\Controllers\Example;

use Peppers\Contracts\FormHandler;
use Peppers\Helpers\ResponseSent;
use Peppers\Helpers\ViewDataStore;
use Peppers\Renderer\HtmlView;
use Peppers\Response;
use Peppers\ServiceLocator;
use Peppers\Services\RequestBody;

class Forms implements FormHandler {

    public function post(): Response|ResponseSent {
        $requestBody = ServiceLocator::get(RequestBody::class);
        $viewDataStore = new ViewDataStore([
            'exampleName' => 'Form submission',
            'action' => '/form/submission',
            'name' => $requestBody->name,
            'email' => $requestBody->email,
            'phone' => $requestBody->phone
        ]);
        $formView = new HtmlView(
                'examples.forms.submission.content',
                $viewDataStore
        );
        return (new Response())->html($formView);
    }

    public function get(): Response {
        $viewDataStore = new ViewDataStore();
        $viewDataStore->exampleName = 'Form submission';
        $viewDataStore->action = '/form/submission';
        $formView = new HtmlView(
                'examples.forms.submission.content',
                $viewDataStore
        );
        return (new Response())->html($formView);
    }

}
