<?php
namespace Awz\BxOrm\Api\Scopes;

use Awz\BxOrm\Api\Type\Parameters as ParametersType;

class Parameters extends ParametersType {

    public function __construct(array $params = array())
    {
        parent::__construct($params);
    }

}