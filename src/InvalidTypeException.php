<?php

namespace ApiBird;

class InvalidTypeException extends \Exception
{

    public function __construct($message, $code, $app = null)
    {
        $app->getDI()->get('response')->setStatusCode($code, $message)->sendHeaders();
        parent::__construct($message);
    }

}
