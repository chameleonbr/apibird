<?php

namespace ApiBird\Error;

class BadRequestException extends \ApiBird\Error\HttpException
{

    public function __construct($message = '')
    {
        parent::__construct('Bad Request', 400, $message);
    }

}