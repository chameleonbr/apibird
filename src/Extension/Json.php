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
        $data = json_decode($data, true);
        if (empty($data) && json_last_error() != JSON_ERROR_NONE) {
            throw new \ApiBird\Error\BadRequestException('Unable to parse data. Check data format.');
        }
        return $data;
    }

    public function toFormat($data)
    {
        $data = json_encode($data, JSON_NUMERIC_CHECK);
        $error = json_last_error();
        if (empty($data) && $error != JSON_ERROR_NONE) {
            throw new \ApiBird\Error\InternalServerErrorException($error);
        }
        return $data;
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
