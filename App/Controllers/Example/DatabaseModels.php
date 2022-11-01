<?php

namespace App\Controllers\Example;

use App\Models\Repositories\PeppersRepository;
use Peppers\Helpers\Http\Request\PathParameter;
use Peppers\Helpers\ResponseSent;
use Peppers\Helpers\ViewDataStore;
use Peppers\Renderer\HtmlView;
use Peppers\Response;
use Peppers\Services\RequestBody;
use Peppers\Helpers\DataValidation\RequestValidator;
use Peppers\Helpers\Http\Request\BodyParameter;
use Peppers\Helpers\DataValidation\IsLatinText;
use Peppers\Helpers\DataValidation\IsEmail;
use Peppers\Helpers\Sql\Conditions;
use Peppers\Helpers\Http\Redirect;
use Peppers\Helpers\Http\Request\QueryParameter;
use Peppers\Helpers\Types\Operator;

class DatabaseModels {

    /**
     * 
     * @return Response|ResponseSent
     */
    public function getForm(PathParameter $formType): Response {
        $response = new Response();
        switch ($formType->getValue()) {
            case 'create':
                return $response->html($this->getCreateForm());

            case 'read':
            case 'update':
            case 'delete':
            default:
                // not implemented
                return $response->setStatusCode(404);
        }
    }

    /**
     * 
     * @param array $errors
     * @return HtmlView
     */
    private function getCreateForm(array $errors = []): HtmlView {
        $store = new ViewDataStore();
        $store->exampleName = 'Create database model';
        $store->action = '/model';
        if ($errors) {
            $store->errors = $errors;
        }

        return new HtmlView(
                'examples.databasemodel.create.content',
                $store
        );
    }

    /**
     * 
     * @return HtmlView
     */
    private function getReadForm(array $data = []): HtmlView {
        $store = new ViewDataStore();
        $store->exampleName = 'Read database model';
        return new HtmlView(
                'examples.databasemodel.read.content',
                $store
        );
    }

    /**
     * 
     * @param RequestBody $body
     * @return Response
     */
    public function create(RequestBody $body): Response {
        $response = new Response();
        // validate input
        $validator = new RequestValidator();
        $validator->check(new BodyParameter('name'))->with(new IsLatinText())
                /* you can chain these methods together */
                ->check(new BodyParameter('email'))->with(new IsEmail());
        if (!$validator->validate()) {
            return $response->html(
                            $this->getCreateForm(
                                    $validator->failed(true)
                            )
            );
        }
        // input valid, store new model
        $repository = new PeppersRepository('default');
        // get a new model instance
        $model = $repository->create();
        $model->name = $body->name;
        $model->email = $body->email;
        // persist new model to database
        $repository->flushCreates();
        // redirect to read form (just for show)
        return $response->setStatusCode(204)
                        ->redirect(new Redirect('/models?allow=read', false));
    }

    /**
     * 
     * @return Response
     */
    public function read(QueryParameter $allow): Response {
        $repository = new PeppersRepository('default');
        if ($allow == 'deleted') {
            return $this->readDeleted();
        }

        /* Conditions class is the WHERE clause of the query, you may nest 
         * as many Condition instances as you like by calling on andCondition()
         * or orCondition(), creating something like 
         * SELECT ... FROM ... WHERE [conditions] 
         *  AND ([more_condition]) 
         *  OR ([more_conditions])
         */
        $conditions = new Conditions();
        $conditions->where('Year(created_on)', Operator::gte, date('Y'));
        $dataPromise = $repository->findByCondition()
                ->select(['name', 'email', 'created_on', 'updated_on'])
                ->where($conditions)
                ->orderBy('created_on', 'desc')
                ->limit(50);
        $models = $dataPromise->resolve();
        $store = new ViewDataStore([
            'models' => $models,
            'formAction' => '/model',
            /* because the data promise hasn't been resolved, no queries are 
             * logged so pass connection to view to get access later */
            'conn' => $repository->getConnection()
        ]);
        switch ($allow) {
            case 'update':
                $viewName = 'examples.databasemodel.update.content';
                $store->columns = ['name', 'email', 'created_on', 'updated_on'];
                $store->exampleName = 'Update database model';
                break;
            case 'read':
                $viewName = 'examples.databasemodel.read.content';
                $store->columns = ['name', 'email', 'created_on', 'updated_on'];
                $store->exampleName = 'Read database model';
                break;
            case 'delete':
                $viewName = 'examples.databasemodel.delete.content';
                $store->columns = ['name', 'email', 'created_on', 'updated_on'];
                $store->exampleName = 'Delete database model';
                break;
        }

        $view = new HtmlView($viewName, $store);
        return (new Response())->html($view);
    }

    /**
     * 
     * @return Response
     */
    private function readDeleted(): Response {
        $repository = new PeppersRepository('default');
        /* Conditions class is the WHERE clause of the query, you may nest 
         * as many Condition instances as you like by calling on andCondition()
         * or orCondition(), creating something like 
         * SELECT ... FROM ... WHERE [conditions] 
         *  AND ([more_condition]) 
         *  OR ([more_conditions])
         */
        $conditions = new Conditions();
        $conditions->where('Year(created_on)', Operator::gte, date('Y'));
        $dataPromise = $repository->findByCondition()
                ->select(['name', 'email', 'created_on', 'updated_on', 'deleted_on'])
                ->where($conditions)
                ->orderBy('deleted_on', 'desc')
                ->limit(50)
                ->withDeleted();
        $models = $dataPromise->resolve();
        $store = new ViewDataStore([
            'models' => $models,
            /* because the data promise hasn't been resolved, no queries are 
             * logged so pass connection to view to get access later */
            'conn' => $repository->getConnection(),
            'columns' => ['name', 'email', 'created_on', 'updated_on', 'deleted_on'],
            'exampleName' => 'Read deleted database models'
        ]);

        $view = new HtmlView('examples.databasemodel.deleted.content', $store);
        return (new Response())->html($view);
    }

    /**
     * 
     * @param PathParameter $name
     * @param PathParameter $email
     * @return Response
     */
    public function update(
            PathParameter $name,
            PathParameter $email
    ): Response {
        $response = new Response();
        // check "old" model data
        $validator = new RequestValidator();
        $validator->check($name)->with(new IsLatinText())
                ->check($email)->with(new IsEmail());
        if (!$validator->validate()) {
            return $response->setStatusCode(404);
        }
        // use the same validator instance to check data to update
        $validator->reset()
                ->check($newName = new BodyParameter('name'))->with(new IsLatinText())
                ->check($newEmail = new BodyParameter('email'))->with(new IsEmail());
        if (!$validator->validate()) {
            return $response->json($validator->failed(true));
        }
        // all is well with data
        $repository = new PeppersRepository();
        if (!($model = $repository->findByPrimaryKey([$name, $email]))) {
            return $response->setStatusCode(404);
        }

        $model->name = $newName;
        $model->email = $newEmail;
        $repository->flushUpdates();
        // reset (user agent) document
        return $response->setStatusCode(205);
    }

    /**
     * 
     * @param PathParameter $name
     * @param PathParameter $email
     * @return Response
     */
    public function delete(
            PathParameter $name,
            PathParameter $email
    ): Response {
        $response = new Response();
        // check "old" model data
        $validator = new RequestValidator();
        $validator->check($name)->with(new IsLatinText())
                ->check($email)->with(new IsEmail());
        if (!$validator->validate()) {
            return $response->setStatusCode(404);
        }
        // all is well with data
        $repository = new PeppersRepository();
        if (!(bool) $repository->deleteByPrimaryKey([$name, $email])) {
            return $response->setStatusCode(404);
        }

        $repository->flushDeletes();
        // reset (user agent) document
        return $response->setStatusCode(205);
    }

}
