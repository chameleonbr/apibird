<?php

namespace ApiBird;

class Service extends \Phalcon\Mvc\Micro
{

    protected $base = 'apibird.';

    public function __construct($dependencyInjector = null, $autoFinish = true)
    {
        $dependencyInjector->set('request', '\\ApiBird\\Request', true);
        $dependencyInjector->set('response', '\\ApiBird\\Response', true);
        parent::__construct($dependencyInjector);
        if ($autoFinish) {
            $this->finish(function () {
                return $this->response->apiSend($this);
            });
        }
    }

    /**
     * Read the Header Content-Type and check if data can be consumed
     * @param type $types
     * @return \ApiBird\Service
     */
    public function consumes($types)
    {
        $ext = $this->request->getContentType();
        $di = $this->getDI();
        if ($di['apibird']->hasRequestExtension($ext, $types)) {
            return $this;
        }
        throw new \ApiBird\InvalidTypeException('Unsupported Media Type', 415, $this);
    }

    /**
     * Read the Header Accept and check if data can be produced
     * @param type $types
     * @return \ApiBird\Service
     */
    public function produces($types)
    {
        $ext = $this->request->getBestAccept();
        $di = $this->getDI();

        if ($di['apibird']->hasResponseExtension($ext, $types)) {
            return $this;
        }
        throw new \ApiBird\InvalidTypeException('Unsupported Media Type', 415, $this);
    }

    /**
     * Get body data after parse type
     * @return array
     */
    public function getBody()
    {
        return $this->request->getBody();
    }

}
