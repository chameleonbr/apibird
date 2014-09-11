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
        $data = file_get_contents('php://input');
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
        if (empty($contentType)) {
            $contentType = $this->getDI()->get('apibird')->getDefaultConsumes();
        }
        return $contentType;
    }

    public function getBestAccept()
    {
        $accept = parent::getBestAccept();
        if ($accept == '*/*') {
            $accept = '';
        }
        if (empty($accept)) {
            $accept = $this->getDI()->get('apibird')->getDefaultProduces();
        }
        return $accept;
    }

}
