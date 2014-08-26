<?php

namespace ApiBird\Error;

class UnsupportedMediaTypeException extends \ApiBird\Error\HttpException
{

    public function __construct($message = '')
    {
        parent::__construct('Unsupported Media Type', 415, $message);
    }

}
