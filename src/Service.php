<?php

namespace ApiBird;

class Service extends \Phalcon\Mvc\Micro
{

    protected $base = 'apibird.';
    protected $extensions = array();
    protected $contentType = null;
    protected $bestAccept = null;

    /**
     * Read the Header Content-Type and check if data can be consumed
     * @param type $types
     * @return \ApiBird\Service
     */
    public function consumes($types)
    {
        $this->contentType = $this->request->getHeader('CONTENT_TYPE');
        if (!empty($this->contentType) &&
                isset($this->extensions[$this->contentType]) &&
                in_array($this->extensions[$this->contentType], $types)) {
            return $this;
        }
        throw new \ApiBird\InvalidTypeException('Unsupported Media Type', $this);
    }

    /**
     * Read the Header Accept and check if data can be produced
     * @param type $types
     * @return \ApiBird\Service
     */
    public function produces($types)
    {
        $this->bestAccept = $this->request->getBestAccept();
        if (!empty($this->bestAccept) &&
                isset($this->extensions[$this->bestAccept]) &&
                in_array($this->extensions[$this->bestAccept], $types)) {
            return $this;
        }
        throw new \ApiBird\InvalidTypeException('Unsupported Media Type', $this);
    }

    /**
     * Register Extensions 
     * @param type $handlers
     * @return \ApiBird\Service
     */
    public function registerExtensions($handlers)
    {
        $di = $this->getDI();
        foreach ($handlers as $index => $handler) {
            $types = $handler::getTypes();
            $this->registerExtension($index, $types);
            $di->set($this->base . $index, function() use ($handler) {
                $instance = new $handler();
                return $instance;
            });
        }
        $accept = $this->request->getBestAccept();
        $this->after(function () use ($accept, $di) {
            $this->response->setHeader('Content-Type', $accept);
            $handler = $this->getExtension($accept);
            return $this->response->setContent($di[$this->base . $handler]->toFormat($this->getReturnedValue()));
        });
        $this->finish(function () {
            return $this->response->sendHeaders()->send();
        });
        return $this;
    }

    /**
     * Register Extension and file types 
     * @param type $serviceName
     * @param type $types
     * @return \ApiBird\Service
     */
    public function registerExtension($serviceName, $types)
    {
        foreach ($types as $value) {
            $this->extensions[$value] = $serviceName;
        }
        return $this;
    }

    /**
     * Get body data after parse type
     * @return array
     */
    public function getBody()
    {
        $handler = $this->extensions[$this->contentType];
        return $this->getDI()
                        ->get($this->base . $handler)
                        ->fromFormat($this->request->getRawBody());
    }

    /**
     * Get extension handler
     * @param string $handler
     * @return \ApiBird\ExtensionInterface
     */
    public function getExtension($handler)
    {
        return $this->extensions[$handler];
    }

}
