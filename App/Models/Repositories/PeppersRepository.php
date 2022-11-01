<?php

namespace App\Models\Repositories;

use App\Models\Peppers;
use Peppers\Base\ModelRepository;
use Peppers\Contracts\Model;

class PeppersRepository extends ModelRepository {

    protected Model $model;
    protected string $table = 'peppers';

    /**
     * 
     * @param string $dotNotationCredentials
     * @param string $dotNotationDataSource
     */
    public function __construct(
            string $dotNotationCredentials = 'default',
            string $dotNotationDataSource = 'default'
    ) {
        $this->model = new Peppers();
        parent::__construct($dotNotationCredentials, $dotNotationDataSource);
    }

}
