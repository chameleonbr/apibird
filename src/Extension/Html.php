<?php

namespace ApiBird\Extension;

class Html implements \ApiBird\ExtensionInterface
{

    protected static $types = [
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
