<?php

namespace ApiBird;

/**
 * @SWG\Model(id="JSendResponse")
 */
class JSend
{

    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const STATUS_ERROR = 'error';

    /**
     * @SWG\Property(name="status",type="string",description="Status de resposta, pode ser success, fail ou error",enum="['success','fail','error']")
     */
    public $status = null;

    /**
     * @SWG\Property(name="data",type="array",description="Dados informativos")
     */
    public $data = null;

    /**
     * @SWG\Property(name="message",type="string",description="Mensagem de acordo com o status")
     */
    public $message = null;

    public function __construct($status = null)
    {
        if (!is_null($status)) {
            $this->status = $status;
        }
    }

    public static function success($data = null)
    {
        $response = new self(self::STATUS_SUCCESS);
        $response->data = $data;
        return $response;
    }

    public static function fail($data = null)
    {
        $response = new self(self::STATUS_FAIL);
        $response->data = $data;
        return $response;
    }

    public static function error($message = null)
    {
        $response = new self(self::STATUS_ERROR);
        $response->message = $message;
        return $response;
    }

    public function __get($key)
    {
        return $this->data->{$key};
    }

    public function __set($key, $val)
    {
        if (!is_object($this->data)) {
            $this->data = new StdClass();
        }
        $this->data->{$key} = $val;
    }

    public function __isset($key)
    {
        return property_exists($this->data, $key);
    }

    public function __unset($key)
    {
        unset($this->data->{$key});
    }

    public function __toString()
    {
        return json_encode($this);
    }

}
