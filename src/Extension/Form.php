<?php

namespace ApiBird\Extension;

class Form extends \Phalcon\DI\Injectable implements \ApiBird\ExtensionInterface
{

    /**
     * Mime types parsed
     * @var array 
     */
    protected static $types = [
        'application/x-www-form-urlencoded',
    ];

    /**
     * Parse type from format
     * @param string $data
     * @return array
     */
    public function fromFormat($data)
    {
        $output = null;
        parse_str($data, $output);
        return $output;
    }

    /**
     * Parse type to format
     * @param array $data
     * @return string
     */
    public function toFormat($data)
    {
        return http_build_query($data);
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
