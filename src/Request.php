<?php

namespace ApiBird;

class Request extends \Phalcon\Http\Request
{
    /**
     *  Return body from specified type
     * @param array $types
     * @return array
     */
    public function getBody($types = [])
    {
        $di = $this->getDI();
        $data = $this->getRawBody();
        $contentType = $this->getContentType();
        $extension = $di['apibird']->getRequestExtension($contentType, $types);
        return $extension->fromFormat($data);
    }

    public function getContentType()
    {
        $contentType = $this->getHeader('CONTENT_TYPE');
        if ($contentType == '*/*') {
            $contentType = '';
        }
        return $contentType;
    }

    public function getBestAccept()
    {
        $accept = parent::getBestAccept();
        if ($accept == '*/*') {
            $accept = '';
        }
        return $accept;
    }

}
