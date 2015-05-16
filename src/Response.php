<?php

namespace ApiBird;

class Response extends \Phalcon\Http\Response
{

    /**
     * 
     * @param type $types
     */
    public function sendResponse($data, $headers = [], $statusCode = 200, $statusText = 'OK')
    {
        $di = $this->getDI();
        $ext = $di['request']->getBestAccept();
        $charset = $di['request']->getProducesCharset();

        if ($charset != 'utf-8') {
            $ext .= '; charset=' . $charset;
            $statusText = mb_convert_encoding($statusText, $charset, 'utf-8');
            mb_convert_variables($charset, 'utf-8', $data);
        }


        $this->setStatusCode($statusCode, $statusText);

        $handler = $di['apibird']->getResponseExtension($ext);

        if (empty($handler)) {
            $handler = $di['apibird']->getDefaultProducesExtension();
        }

        if (is_callable($data)) {
            $typeName = $di['apibird']->getExtensionHandlerName($ext);
            if ($typeName) {
                $data = $data($typeName);
            } else {
                $data = $data();
            }
        }

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        $fn = $di['apibird']->getDataHandler();
        if (is_callable($fn)) {
            $data = $fn($data, $statusCode, $statusText);
        }
        $this->setHeader('Content-Type', $ext);

        if (is_array($headers)) {
            foreach ($headers as $key => $value) {
                $this->setHeader($key, $value);
            }
        } else {
            $this->setHeaders($headers);
        }
        $this->setContent($handler->toFormat($data));
        return $this->sendHeaders()->send()->exitOnError($statusCode);
    }

    public function exitOnError($status = 200)
    {
        if ($status >= 400) {
            exit();
        }
        return $this;
    }

    public function setHeaders(Phalcon\Http\Response\HeadersInterface $headers)
    {
        if (!empty($headers) && is_array($headers)) {
            foreach ($headers as $name => $value) {
                $this->setHeader($name, $value);
            }
        } elseif (!empty($headers)) {
            parent::setHeaders($headers);
        }
    }

    /**
     * Return data with HTTP status 200
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function ok($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 200, 'OK');
    }

    /**
     * Return data with HTTP status 201
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function created($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 201, 'Created');
    }

    /**
     * Return data with HTTP status 202
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function accepted($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 202, 'Accepted');
    }

    /**
     * Return data with HTTP status 204
     * @param array $headers
     * @return mixed
     */
    public function noContent($headers = [])
    {
        //var_dump($headers);
        return $this->sendResponse(null, $headers, 204, 'No Content');
    }

    /**
     * Return data with HTTP status 206
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function partialContent($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 206, 'Partial Content');
    }

    /**
     * Return data with HTTP status 400
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function badRequest($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 400, 'Bad Request');
    }

    /**
     * Return data with HTTP status 401
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function unauthorized($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 401, 'Unauthorized');
    }

    /**
     * Return data with HTTP status 402
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function paymentRequired($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 402, 'Payment Required');
    }

    /**
     * Return data with HTTP status 403
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function forbidden($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 403, 'Forbidden');
    }

    /**
     * Return data with HTTP status 404
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function notFound($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 404, 'Not Found');
    }

    /**
     * Return data with HTTP status 405
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function methodNotAllowed($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 405, 'Method not Allowed');
    }

    /**
     * Return data with HTTP status 406
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function notAcceptable($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 406, 'Not Acceptable');
    }

    /**
     * Return data with HTTP status 409
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function conflict($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 409, 'Conflict');
    }

    /**
     * Return data with HTTP status 411
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function lengthRequired($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 411, 'Length Required');
    }

    /**
     * Return data with HTTP status 412
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function preconditionFailed($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 412, 'Precondition Failed');
    }

    /**
     * Return data with HTTP status 413
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function requestEntityTooLarge($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 413, 'Request Entity Too Large');
    }

    /**
     * Return data with HTTP status 415
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function unsupportedMediaType($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 415, 'Unsupported Media Type');
    }

    /**
     * Return data with HTTP status 429
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function tooManyRequests($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 429, 'Too Many Requests');
    }

    /**
     * Return data with HTTP status 500
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function internalServerError($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 500, 'Internal Server Error');
    }

    /**
     * Return data with HTTP status 501
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function notImplemented($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 501, 'Not Implemented');
    }

    /**
     * Return data with HTTP status 502
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function badGateway($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 502, 'Bad Gateway');
    }

    /**
     * Return data with HTTP status 503
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function serviceUnavailable($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 503, 'Service Unavailable');
    }

    /**
     * Return data with HTTP status 504
     * @param mixed $data
     * @param array $headers
     * @return mixed
     */
    public function gatewayTimeout($data = [], $headers = [])
    {
        return $this->sendResponse($data, $headers, 504, 'Gateway Timeout');
    }

}
