<?php

use Peppers\Helpers\Http\Route;

return [
    Route::get('/', function () {
        $dataStore = new \Peppers\Helpers\ViewDataStore();
        $welcomeView = new Peppers\Renderer\HtmlView(
                'welcome-to-peppers',
                $dataStore
        );
        return (new Peppers\Response())->html($welcomeView);
    })->setAllowFreeQuery(false),
    Route::get('/routing/{param1}/{param2}', [App\Controllers\Example\Routing::class, 'readFromRequest'])
            ->setPathExpression('param1', '.+')
            ->setPathExpression('param2', '.+')
            ->setAllowFreeQuery(false),
    Route::get('/view-composition', [App\Controllers\Example\ViewComposition::class, 'viewComposition'])
            ->setPathExpression('param1', '.+')
            ->setPathExpression('param2', '.+')
            ->setAllowFreeQuery(false),
    Route::form('/form/submission', App\Controllers\Example\Forms::class),
    Route::form('/form/validation', App\Controllers\Example\InputValidation::class),
    Route::get('/redirect', [App\Controllers\Example\Routing::class, 'redirect']),
    Route::get('/dependency-injection', [App\Controllers\Example\DependencyInjection::class, 'inject'])
            ->setAllowFreeQuery(false),
    Route::get('/model/{formType}', [App\Controllers\Example\DatabaseModels::class, 'getForm'])
            ->setPathExpression('formType', '[a-z]+') /* .. or you could have used this regular expression: (create|read|update|delete) */
            ->setAllowFreeQuery(false),
    Route::get('/models', [App\Controllers\Example\DatabaseModels::class, 'read'])
            ->setQueryExpression('allow', '(read|update|delete|deleted)')
            ->setAllowFreeQuery(false),
    Route::post('/model', [App\Controllers\Example\DatabaseModels::class, 'create'])
            ->setAllowFreeQuery(false),
    Route::post('/model/{name}/{email}', [App\Controllers\Example\DatabaseModels::class, 'update'])
            ->setPathExpression('name', '.+')
            ->setPathExpression('email', '.+')
            ->setAllowFreeQuery(false),
    Route::delete('/model/{name}/{email}', [App\Controllers\Example\DatabaseModels::class, 'delete'])
            ->setPathExpression('name', '.+')
            ->setPathExpression('email', '.+')
            ->setAllowFreeQuery(false),
    Route::get('/file/create-random', [App\Controllers\Example\Files::class, 'createRandomFile'])
            ->setAllowFreeQuery(false),
    Route::get('/file/{formType}', [App\Controllers\Example\Files::class, 'getForm'])
            ->setPathExpression('formType', '[a-z]+')
            ->setAllowFreeQuery(false),
    Route::post('/file', [App\Controllers\Example\Files::class, 'upload'])
            ->setAllowFreeQuery(false),
    Route::get('/download/asset', [App\Controllers\Example\Files::class, 'downloadAsset'])
            ->setAllowFreeQuery(false),
    Route::get('/events/block', [App\Controllers\Example\Events::class, 'blocking'])
            ->setAllowFreeQuery(false),
    Route::get('/events/defer', [App\Controllers\Example\Events::class, 'deferred'])
            ->setAllowFreeQuery(false),
];
