<?php

namespace ApiBird;

class ServiceProvider extends \Phalcon\DI\Injectable
{

    protected $base = 'apibird.';
    protected $extensions = array();
    protected $defaultProduces = null;
    protected $defaultConsumes = null;
    protected $corsEnabled = false;
    protected $dataHandler = null;

    public function __construct()
    {
        $this->setDataHandler(function($data = null, $code = null, $message = null) {
            return $data;
        });
    }

    /**
     * Register Extensions 
     * @param type $handlers
     * @return \ApiBird\Micro
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
            }, true);
        }
        return $this;
    }

    /**
     * Register Extension and file types 
     * @param type $serviceName
     * @param type $types
     * @return \ApiBird\Micro
     */
    public function registerExtension($serviceName, $types)
    {
        foreach ($types as $value) {
            $this->extensions[$value] = $serviceName;
        }
        return $this;
    }

    public function getRequestExtension($fileType = '', $acceptedFileTypes = [])
    {
        return $this->getExtension($fileType, $acceptedFileTypes, $this->defaultConsumes);
    }

    public function getResponseExtension($fileType = '', $acceptedFileTypes = [])
    {
        return $this->getExtension($fileType, $acceptedFileTypes, $this->defaultProduces);
    }

    /**
     * 
     * @param string $fileType
     * @param array $acceptedFileTypes
     * @return \ApiBird\ExtensionInterface
     * @throws \ApiBird\InvalidTypeException
     */
    public function getExtension($fileType = '', $acceptedFileTypes = [], $defaultType = '')
    {
        if (empty($fileType) && !empty($defaultType)) {
            return $this->getDI()->get($this->base . $defaultType);
        } else if (!empty($fileType) && isset($this->extensions[$fileType])) {
            if (!empty($acceptedFileTypes) && in_array($this->extensions[$fileType], $acceptedFileTypes)) {
                return $this->getDI()->get($this->base . $this->extensions[$fileType]);
            } else {
                return $this->getDI()->get($this->base . $this->extensions[$fileType]);
            }
        }
    }

    /**
     * Return true if has configured extension
     * @param string $fileType
     * @param array $acceptedFileTypes
     * @param string $defaultType
     * @return boolean
     */
    protected function hasExtension($fileType = '', $acceptedFileTypes = [], $defaultType = '')
    {
        $di = $this->getDI();
        if ((empty($fileType)) &&
                $di->has($this->base . $defaultType)) {
            return true;
        } elseif (!empty($acceptedFileTypes) &&
                isset($this->extensions[$fileType]) &&
                in_array($this->extensions[$fileType], $acceptedFileTypes)
        ) {
            return true;
        } elseif (empty($acceptedFileTypes) &&
                isset($this->extensions[$fileType])) {
            return true;
        }
        return false;
    }

    /**
     * @see hasExtension
     * @param string $fileType
     * @param array $acceptedFileTypes
     * @return boolean
     */
    public function hasRequestExtension($fileType = '', $acceptedFileTypes = [])
    {
        return $this->hasExtension($fileType, $acceptedFileTypes, $this->defaultConsumes);
    }

    /**
     * @see hasExtension
     * @param string $fileType
     * @param array $acceptedFileTypes
     * @return boolean
     */
    public function hasResponseExtension($fileType = '', $acceptedFileTypes = [])
    {
        return $this->hasExtension($fileType, $acceptedFileTypes, $this->defaultProduces);
    }

    /**
     * Set default Consumes type
     * @param string $type
     * @return \ApiBird\ServiceProvider
     */
    public function setDefaultConsumes($type)
    {
        $this->defaultConsumes = $type;
        return $this;
    }

    /**
     * Set default Produces type
     * @param string $type
     * @return \ApiBird\ServiceProvider
     */
    public function setDefaultProduces($type)
    {
        $this->defaultProduces = $type;
        return $this;
    }

    public function getDefaultConsumes()
    {
        $ext = $this->getDefaultConsumesExtension()->getTypes();
        return $ext[0];
    }

    public function getDefaultProduces()
    {
        $ext = $this->getDefaultProducesExtension()->getTypes();
        return $ext[0];
    }

    public function getDefaultConsumesExtension()
    {
        if (!empty($this->defaultConsumes)) {
            return $this->getDI()->get($this->base . $this->defaultConsumes);
        }
        return false;
    }

    public function getDefaultProducesExtension()
    {
        if (!empty($this->defaultProduces)) {
            return $this->getDI()->get($this->base . $this->defaultProduces);
        }
        return false;
    }

    public function enableCors()
    {
        $this->corsEnabled = true;
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
            header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE,HEAD,OPTIONS,PATCH");
        }
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        } else {
            header("Access-Control-Allow-Headers: X-Accept-Charset,X-Accept,Accept,Content-Type,Location,Authorization");
        }
        header("Access-Control-Expose-Headers: Location,Link,ETag");
        return $this;
    }

    public function corsEnabled()
    {
        return $this->corsEnabled;
    }

    public function setDataHandler($function)
    {
        $this->dataHandler = $function;
        return $this;
    }

    public function getDataHandler()
    {
        return $this->dataHandler;
    }

}
