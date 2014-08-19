<?php

namespace ApiBird\Handler;

class Json implements \ApiBird\HandlerInterface
{

    public static $types = [
        'application/json',
        'text/javascript',
        'application/javascript',
        'application/ecmascript',
    ];

    public function fromFormat($data)
    {
        return json_decode($data, true);
    }

    public function toFormat($data)
    {
        return json_encode($data);
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
