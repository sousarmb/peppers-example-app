<?php

namespace App\Controllers\Example;

use DateTime;
use Settings;
use Peppers\Helpers\DataValidation\RequestValidator;
use Peppers\Helpers\DataValidation;
use Peppers\Helpers\Http\Request\FileParameter;
use Peppers\Helpers\Http\Request\PathParameter;
use Peppers\Helpers\LocalFile;
use Peppers\Helpers\ResponseSent;
use Peppers\Helpers\ViewDataStore;
use Peppers\Renderer\HtmlView;
use Peppers\Response;

class Files {

    /**
     * 
     * @return Response
     */
    public function createRandomFile(): Response {
        $now = (new DateTime('now'))->format('Y-m-d H:i:s');
        $file = new LocalFile();
        // write some data on it
        $file->write("Text file generated on - $now - for this request alone!" . PHP_EOL);
        $file->write('Check your filesystem at ' . Settings::get('PRIVATE_DIR') . PHP_EOL);
        // if not closed here, the response strategy would do it for you
        $file->close();
        /* if the call below happens file is deleted at the end of request 
         * processing if possible */
        // $file->setAsTemporary();
        /* force download prompt on client's browser; necessary HTTP headers 
         * will be set by framework code */
        return (new Response())->file($file);
    }

    /**
     * 
     * @return Response|ResponseSent
     */
    public function getForm(PathParameter $formType): Response {
        $response = new Response();
        switch ($formType->getValue()) {
            case 'upload':
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
        $store->exampleName = 'Upload file';
        $store->action = '/file';
        if ($errors) {
            $store->errors = $errors;
        }

        return new HtmlView(
                'examples.files.submission.content',
                $store
        );
    }

    /**
     * 
     * @param FileParameter $file
     * @param Response $response
     * @return Response
     */
    public function upload(
            FileParameter $file,
            Response $response
    ): Response {
        $validator = new RequestValidator();
        $validator->check($file)
                ->with((new DataValidation\IsFile())->checkIsUpload());
        /* want to force an error? set MIME type to 'application/pdf' and 
         * submit some other file type */
        if (!$validator->validate()) {
            return $response->html(
                            $this->getCreateForm(
                                    $validator->failed(true)
                            )
            );
        }
        // all is well, store the file in the asset directory: /private
        $file->getFile()->moveTo($file->getFileName(), true);
        // present the result
        $store = new ViewDataStore();
        $store->exampleName = 'Upload file';
        $store->action = '/file';
        $store->file = $file->getFileName();
        $store->mimetype = $file->getMimeType();
        return $response->html(new HtmlView(
                                'examples.files.submission.content',
                                $store
                        )
        );
    }

    /**
     * 
     * @return Response
     */
    public function downloadAsset(): Response {
        /* open an existing file from 'private' directory; all assets not 
         * accessible from the internet must be stored there */
        $file = new LocalFile('lots-of-peppers.png', 'r');
        $response = new Response();
        $response->file($file);
        /* if you don't set (all necessary HTTP) headers, framework code will 
         * do its best to guess which are necessary */
        $response->setHeader('content-length', filesize($file->getPath()));
        // business as usual
        return $response;
    }

}
