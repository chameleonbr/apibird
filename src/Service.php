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
        /* set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
          throw new \ApiBird\Error\BadRequestException();
          }); */
        if ($this->options['autoFinish']) {
            $this->finish(function () {
                return $this->response->apiSend($this);
            });
        }
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
        throw new \ApiBird\Error\UnsupportedMediaTypeException();
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
        throw new \ApiBird\Error\UnsupportedMediaTypeException();
    }

    public function producesExcept($types = [])
    {
        
    }

    public function serverCache($dataReceived, $function, $limit = 3600)
    {
        $di = $this->getDI();
        if ($di->has($this->options['cacheService'])) {
            $hash = $this->getHash($dataReceived);
            $dataCache = $di['cache']->get($hash);
            if (!$dataCache) {
                $dataReturn = $function($dataReceived);
                $di['cache']->save($hash, $dataReturn, $limit);
            } else {
                $dataReturn = $dataCache;
            }
        } else {
            $dataReturn = $function($dataReceived);
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
