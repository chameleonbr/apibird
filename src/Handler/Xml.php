<?php

namespace ApiBird\Handler;

class Xml implements \ApiBird\HandlerInterface
{

    public static $types = [
        'application/xml',
    ];

    public function fromFormat($data)
    {
        $xml = simplexml_load_string($data);
        $json = json_encode($xml);
        return json_decode($json, TRUE);
    }

    public function toFormat($data, $root = 'result')
    {
        $xml = \LSS\Array2XML::createXML($root, $data);
        return $xml->saveXML();
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
