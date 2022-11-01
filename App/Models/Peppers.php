<?php

namespace App\Models;

use Peppers\Base\Model;

class Peppers extends Model {

    protected array $columns = [
        'name',
        'email',
        'created_on',
        'updated_on',
        'deleted_on'
    ];
    protected array $primaryKeyColumns = [
        'name',
        'email'
    ];
    protected array $protectedColumns = [
        'created_on',
        'deleted_on',
        'updated_on'
    ];

    /**
     * $data must be a key => value map where the keys are the set or a subset
     * of the model members
     * 
     * @param array|null $modelData
     */
    public function __construct(?array $modelData = null) {
        parent::__construct($modelData);
    }

}
