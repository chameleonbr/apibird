<?php

namespace ApiBird;

class Request extends \Phalcon\Http\Request
{

    protected $consumesCharset = 'utf-8';
    protected $producesCharset = 'utf-8';

    /**
     *  Return body from specified type
     * @param array $types
     * @return array
     */
    public function getBody($types = [])
    {
        $di = $this->getDI();
        $data = file_get_contents('php://input');
        if ($this->consumesCharset != 'utf-8') {
            $data = mb_convert_encoding($data, 'utf-8', $this->consumesCharset);
        }
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

        if (strpos($contentType, ';') !== false) {
            $tmp = explode(';', $contentType);
            $contentType = $tmp[0];
            $this->consumesCharset = $this->parseCharset($tmp[1]);
        }

        if (empty($contentType)) {
            $contentType = $this->getDI()->get('apibird')->getDefaultConsumes();
        }
        return $contentType;
    }

    protected function parseCharset($headerParams = '')
    {
        $pos = strpos($headerParams, 'charset=');
        if ($pos !== false) {
            $charset = substr($headerParams, $pos);
        }
        return strtolower($charset);
    }

    public function getBestAccept()
    {
        $accept = parent::getBestAccept();
        if ($accept == '*/*') {
            $accept = '';
        }

        if (strpos($accept, ';') !== false) {
            $tmp = explode(';', $accept);
            $accept = $tmp[0];
            $this->producesCharset = $this->parseCharset($tmp[1]);
        }

        if (empty($accept)) {
            $accept = $this->getDI()->get('apibird')->getDefaultProduces();
        }

        return $accept;
    }

    public function getProducesCharset()
    {
        return $this->producesCharset;
    }

}
