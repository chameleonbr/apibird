<?php

namespace ApiBird;

class InvalidTypeException extends \Exception
{

    public function __construct($message, $app = null)
    {
        $app->getDI()->get('response')->setStatusCode(415, $message)->sendHeaders();
        parent::__construct($message);
    }

}
