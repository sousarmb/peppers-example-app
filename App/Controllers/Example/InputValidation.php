<?php

namespace App\Controllers\Example;

use Peppers\Contracts\FormHandler;
use Peppers\Helpers\ResponseSent;
use Peppers\Helpers\ViewDataStore;
use Peppers\Renderer\HtmlView;
use Peppers\Response;
use Peppers\ServiceLocator;
use Peppers\Services\RequestBody;
use Peppers\Helpers\DataValidation;
use Peppers\Helpers\Http\Request\BodyParameter;

class InputValidation implements FormHandler {

    public function post(): Response|ResponseSent {
        $validator = new DataValidation\RequestValidator();
        $validator->check(new BodyParameter('name'), false)
                ->with((new DataValidation\IsLatinText())->setMaxLength(128));
        $validator->check(new BodyParameter('email'), false)
                ->with((new DataValidation\IsEmail())->setForbidden(['admin@peppers.local']));
        $validator->check(new BodyParameter('phone'), false)
                /* you can chain multiple validators for one input field */
                ->with(new DataValidation\IsInteger())
                ->with(new DataValidation\RegularExpression('/\d{9,15}/'));

        $requestBody = ServiceLocator::get(RequestBody::class);
        // pass the store constructor all data
        $viewDataStore = new ViewDataStore([
            'exampleName' => 'Input validation',
            'action' => '/form/validation',
            'name' => $requestBody->name,
            'email' => $requestBody->email,
            'phone' => $requestBody->phone,
            'errors' => $validator->failed(true)
        ]);
        $formView = new HtmlView(
                'examples.forms.submission.content',
                $viewDataStore
        );
        return (new Response())->html($formView);
    }

    public function get(): Response {
        $viewDataStore = new ViewDataStore();
        // set individual parameters in the store
        $viewDataStore->exampleName = 'Input Validation';
        $viewDataStore->action = '/form/validation';
        $formView = new HtmlView(
                'examples.forms.submission.content',
                $viewDataStore
        );
        return (new Response())->html($formView);
    }

}
