<?php

namespace ApiBird\Extension;

use \LSS\Array2XML;

class Xml implements \ApiBird\ExtensionInterface
{

    protected static $types = [
        'application/xml',
        'text/xml'
    ];

    public function fromFormat($data)
    {
        $xml = simplexml_load_string($data);
        $json = json_encode($xml);
        return json_decode($json, TRUE);
    }

    public function toFormat($data, $root = 'result')
    {
        Array2XML::init('1.0', 'UTF-8', false);
        $xml = Array2XML::createXML($root, $data);
        return $xml->saveXML();
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
