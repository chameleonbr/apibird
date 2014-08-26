<?php

namespace ApiBird\Error;

class HttpException extends \Exception
{

    public function __construct($header = '', $code = null, $message = null, $previous = null)
    {
        $this->header = $header;
        $this->message = $message;
        $this->code = $code;
        parent::__construct($message, $code, $previous);
        header('HTTP/1.1 ' . $this->code . ' ' . $this->header, true);
        echo $message;
        exit();
    }

}
