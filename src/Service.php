<?php

namespace ApiBird;

class Service extends \Phalcon\Mvc\Micro
{

    protected $base = 'apibird.';
    protected $handlers = array();
    protected $contentType = null;
    protected $bestAccept = null;

    /**
     * $handlers[application/json] = json
     * 
     * Read the Header Content-Type
     * @param type $types
     * @return \ApiBird\Service
     */
    public function consumes($types)
    {
        $this->contentType = $this->request->getHeader('CONTENT_TYPE');
        if (!empty($this->contentType) &&
                isset($this->handlers[$this->contentType]) &&
                in_array($this->handlers[$this->contentType], $types)) {
            return $this;
        }
        throw new \ApiBird\InvalidTypeException('Unsupported Media Type',$this);
    }

    /**
     * Read the Header Accept
     * @param type $types
     * @return \ApiBird\Service
     */
    public function produces($types)
    {
        $this->bestAccept = $this->request->getBestAccept();
        if (!empty($this->bestAccept) &&
                isset($this->handlers[$this->bestAccept]) &&
                in_array($this->handlers[$this->bestAccept], $types)) {
            return $this;
        }
        throw new \ApiBird\InvalidTypeException('Unsupported Media Type',$this);
    }

    public function registerHandlers($handlers)
    {
        $di = $this->getDI();
        foreach ($handlers as $index => $handler) {
            $types = $handler::getTypes();
            $this->registerHandler($index, $types);
            $di->set($this->base . $index, function() use ($handler) {
                $instance = new $handler();
                return $instance;
            });
        }
        $accept = $this->request->getBestAccept();
        $this->after(function () use ($accept, $di) {
            $this->response->setHeader('Content-Type', $accept);
            $handler = $this->getHandler($accept);
            return $this->response->setContent($di[$this->base . $handler]->toFormat($this->getReturnedValue()));
        });
        $this->finish(function () {
            return $this->response->sendHeaders()->send();
        });
        return $this;
    }

    public function registerHandler($serviceName, $types)
    {
        foreach ($types as $value) {
            $this->handlers[$value] = $serviceName;
        }
        return $this;
    }

    public function getData()
    {
        $handler = $this->handlers[$this->contentType];
        return $this->getDI()
                        ->get($this->base . $handler)
                        ->fromFormat($this->request->getRawBody());
    }

    public function getHandler($handler)
    {
        return $this->handlers[$handler];
    }

}
