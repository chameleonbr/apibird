<?php

namespace ApiBird\Handler;

class Html implements \ApiBird\HandlerInterface
{

    public static $types = [
        'text/html',
        'application/xhtml+xml',
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
