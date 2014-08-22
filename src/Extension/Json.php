<?php

namespace ApiBird\Extension;

class Json implements \ApiBird\ExtensionInterface
{

    protected static $types = [
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
        return json_encode($data,JSON_NUMERIC_CHECK);
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
