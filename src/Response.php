<?php

namespace ApiBird;

class Response extends \Phalcon\Http\Response
{

    /**
     * 
     * @param type $types
     */
    public function apiSend($app)
    {
        $di = $this->getDI();
        $ext = $di['request']->getBestAccept();
        $this->setHeader('Content-Type', $ext);
        $handler = $di['apibird']->getResponseExtension($ext);
        $this->setContent($handler->toFormat($app->getReturnedValue()));
        return $this->sendHeaders()->send();
    }

    public function ok($data = [], $headers = [])
    {
        $di = $this->getDI();
        $ext = $di['request']->getBestAccept();
        $this->setHeader('Content-Type', $ext);
        $handler = $di['apibird']->getResponseExtension($ext);
        $this->setContent($handler->toFormat($data));
        return $this->sendHeaders()->send();
    }

    public function created($data = [], $headers = [])
    {
        
    }

    public function accepted($data = [], $headers = [])
    {
        
    }

    public function noContent($headers = [])
    {
        
    }
    
    public function partialContent($data = [], $headers = [])
    {
        
    }
    
    public function badRequest($data = [], $headers = [])
    {
        
    }

    public function unauthorized($data = [], $headers = [])
    {
        
    }
    public function paymentRequired($data = [], $headers = [])
    {
        
    }
    public function forbidden($data = [], $headers = [])
    {
        
    }
    public function notFound($data = [], $headers = [])
    {
        
    }
    public function methodNotAllowed($data = [], $headers = [])
    {
        
    }
    public function notAcceptable($data = [], $headers = [])
    {
        
    }
    public function conflict($data = [], $headers = [])
    {
        
    }
    public function lengthRequired($data = [], $headers = [])
    {
        
    }
    public function preconditionFailed($data = [], $headers = [])
    {
        
    }
    public function requestEntityTooLarge($data = [], $headers = [])
    {
        
    }
    public function unsupportedMediaType($data = [], $headers = [])
    {
        
    }
    public function tooManyRequests($data = [], $headers = [])
    {
        
    }

}
