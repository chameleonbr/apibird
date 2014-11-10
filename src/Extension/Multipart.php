<?php

namespace ApiBird\Extension;

class Multipart extends \Phalcon\DI\Injectable implements \ApiBird\ExtensionInterface
{

    /**
     * Mime types parsed
     * @var array 
     */
    protected static $types = [
        'multipart/form-data',
    ];

    /**
     * Parse type from format
     * @param string $data
     * @return array
     */
    public function fromFormat($data)
    {
        $input = file_get_contents('php://input');
        $output = $this->parseRawHttpRequest($input);
        return $output;
    }

    /**
     * Parse type to format
     * @param array $data
     * @return string
     */
    public function toFormat($data)
    {
        $di = \Phalcon\DI\FactoryDefault::getDefault();
        $di['response']->internalServerError('Unable to write format.');
    }

    public static function getTypes()
    {
        return static::$types;
    }

    /**
     * Parse raw HTTP request data
     *
     * Any files found in the request will be added by their field name to the
     * $data['files'] array.
     * See more at: http://www.chlab.ch/blog/archives/php/manually-parse-raw-http-data-php#sthash.h1ntHeDs.dpuf
     * @return  array  Associative array of request data
     */
    public function parseRawHttpRequest($input)
    {
        $a_data = array();
        $matches = null;
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        if (!count($matches)) {
            parse_str(urldecode($input), $a_data);
            return $a_data;
        }
        $boundary = $matches[1];
        $a_blocks = preg_split("/-+$boundary/", $input);
        array_pop($a_blocks);
        foreach ($a_blocks as $block) {
            if (empty($block)) {
                continue;
            }
            if (strpos($block, 'application/octet-stream') !== FALSE) {
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
                $a_data['files'][$matches[1]] = $matches[2];
            } else {
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                $a_data[$matches[1]] = $matches[2];
            }
        }
        return $a_data;
    }

}
