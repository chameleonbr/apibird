<?php

namespace ApiBird;

class Service extends \Phalcon\Mvc\Micro
{

    protected $base = 'apibird.';
    protected $options = [];

    public function __construct($dependencyInjector = null, $options = [])
    {
        $defaultOptions = [
            'autoFinish' => true,
            'cacheService' => 'cache',
        ];

        $this->options = array_merge($defaultOptions, $options);

        $dependencyInjector->set('request', '\\ApiBird\\Request', true);
        $dependencyInjector->set('response', '\\ApiBird\\Response', true);

        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
        parent::__construct($dependencyInjector);
    }

    /**
     * Read the Header Content-Type and check if data can be consumed
     * @param type $types
     * @return \ApiBird\Service
     */
    public function consumes($types = [])
    {
        $ext = $this->request->getContentType();
        $di = $this->getDI();
        if ($di['apibird']->hasRequestExtension($ext, $types)) {
            return $this;
        }
        return $this->response->unsupportedMediaType();
        //throw new \ApiBird\Error\UnsupportedMediaTypeException();
    }

    public function consumesExcept($types = [])
    {
        
    }

    /**
     * Read the Header Accept and check if data can be produced
     * @param type $types
     * @return \ApiBird\Service
     */
    public function produces($types = [])
    {
        $ext = $this->request->getBestAccept();
        $di = $this->getDI();

        if ($di['apibird']->hasResponseExtension($ext, $types)) {
            return $this;
        }
        
        return $this->response->unsupportedMediaType();
        //throw new \ApiBird\Error\UnsupportedMediaTypeException();
    }

    public function producesExcept($types = [])
    {
        
    }

    public function serverCache($dataReceived, $function, $limit = 3600)
    {
        if ($this->getDI()->has($this->options['cacheService'])) {
            $hash = $this->getHash($dataReceived);
            $dataReturn = $this->getDataCache($dataReceived, $hash, $function, $limit);
        } else {
            $dataReturn = $function($dataReceived);
        }
        return $dataReturn;
    }

    protected function getDataCache($dataReceived, $hash, $function, $limit)
    {
        $realLimit = 86400;
        $di = $this->getDI();
        $dataCache = $di['cache']->get($hash);
        if (!empty($dataCache) && time() >= $dataCache['expires']) {
            try {
                $dataReturn = $function($dataReceived);
                $di['cache']->save($hash, ['data' => $dataReturn, 'expires' => time() + $limit], $realLimit);
            } catch (\Exception $e) {
                $dataReturn = $dataCache['data'];
            }
        } elseif (empty($dataCache)) {
            $this->internalServerError();
            exit();
        } else {
            $dataReturn = $dataCache['data'];
        }

        return $dataReturn;
    }

    /**
     * Get body data after parse type
     * @return array
     */
    public function getBody()
    {
        return $this->request->getBody();
    }

    public function getHash($data)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $this->getRouter()->getRewriteUri();
        $hash = md5($method . $path . json_encode($data));
        return $hash;
    }
}
