<?php

namespace ApiBird;

class Response extends \Phalcon\Http\Response
{

    /**
     * 
     * @param type $types
     */
    public function sendApiResponse($app)
    {    
        $di = $this->getDI();
        $ext = $di['request']->getBestAccept();
        $this->setHeader('Content-Type', $ext);
        $handler = $di['apibird']->getResponseExtension($ext);
        $this->setContent($handler->toFormat($app->getReturnedValue()));
        return $this->sendHeaders()->send();
    }

}
