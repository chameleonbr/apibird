<?php

namespace ApiBird\Error;

class InternalServerErrorException extends \ApiBird\Error\HttpException
{

    public function __construct($message = '')
    {
        parent::__construct('Internal Server Error', 500, $message);
    }

}
