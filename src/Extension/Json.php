<?php

namespace ApiBird\Extension;

class Json extends \Phalcon\DI\Injectable implements \ApiBird\ExtensionInterface
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
            $di = \Phalcon\DI\FactoryDefault::getDefault();
            $di['response']->badRequest('Unable to parse data. Check format.');
        }
        return $data;
    }

    public function toFormat($data)
    {
        $callback = (isset($_GET['callback']) && !empty($_GET['callback'])) ? ($_GET['callback'].'(') : ('');
        $suffix = (!empty($callback)) ? (');') : ('');
        $data = $callback . json_encode($data, JSON_NUMERIC_CHECK) . $suffix;
        $error = json_last_error();
        if (empty($data) && $error != JSON_ERROR_NONE) {
            $di = \Phalcon\DI\FactoryDefault::getDefault();
            $di['response']->internalServerError('Unable to write format.');
        }
        return $data;
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
