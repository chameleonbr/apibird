<?php

namespace ApiBird\Extension;

use \LSS\Array2XML;

class Xml implements \ApiBird\ExtensionInterface
{

    protected static $types = [
        'application/xml',
        'text/xml'
    ];

    protected $options = ['root' => 'result','list' => 'list'];
    
    public function fromFormat($data)
    {
        $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOWARNING | LIBXML_NOERROR);
        if ($xml) {
            $json = json_encode($xml);
            return json_decode($json, true);
        } else {
            throw new \ApiBird\Error\BadRequestException('Unable to parse data. Check data format.');
        }
    }

    public function toFormat($data)
    {
        try {
            if (!is_int(key($data))) {
                Array2XML::init('1.0', 'UTF-8', false);
                $xml = Array2XML::createXML($this->options['root'], $data);
                return $xml->saveXML();
            }else{
                Array2XML::init('1.0', 'UTF-8', false);
                $xml = Array2XML::createXML($this->options['list'], array($this->options['root'] => $data));
                return $xml->saveXML();
            }
        } catch (\Exception $e) {
            throw new \ApiBird\Error\InternalServerErrorException($e->getMessage());
        }
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
