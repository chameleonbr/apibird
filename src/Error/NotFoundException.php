<?php

namespace ApiBird\Error;

class NotFoundException extends \ApiBird\Error\HttpException
{

    public function __construct($message = '')
    {
        parent::__construct('Not Found', 404, $message);
    }

}
