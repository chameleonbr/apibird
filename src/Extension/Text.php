<?php

namespace ApiBird\Extension;

class Text implements \ApiBird\ExtensionInterface
{

    protected static $types = [
        'plain/text'
    ];

    public function fromFormat($data)
    {
        return $data;
    }

    public function toFormat($data)
    {
        return $data;
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
