<?php

namespace ApiBird;

class Request extends \Phalcon\Http\Request
{

    /**
     * 
     * @param type $types
     * @return type
     */
    public function getBody($types = [])
    {
        $di = $this->getDI();
        $data = $this->getRawBody();
        $contentType = $di['request']->getHeader('CONTENT_TYPE');
        $extension = $di['apibird']->getRequestExtension($contentType, $types);
        return $extension->fromFormat($data);
    }

}
