<?php

namespace ApiBird;

class Response extends \Phalcon\Http\Response
{

    /**
     * 
     * @param type $types
     */
    public function sendApiResponse($types = [])
    {
        $di = $this->getDI();
        $bestAccept = $di['request']->getBestAccept();
        $extension = $di['apibird']->getResponseExtension($bestAccept, $types);
        $this->setContent($extension->toFormat($this->getContent()));
        parent::send();
    }

}
