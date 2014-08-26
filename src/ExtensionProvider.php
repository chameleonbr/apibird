<?php

namespace ApiBird;

class ExtensionProvider extends \Phalcon\DI\Injectable
{

    protected $base = 'apibird.';
    protected $extensions = array();
    protected $defaultProduces = null;
    protected $defaultConsumes = null;

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
            }, true);
        }
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

    public function getRequestExtension($fileType = '', $acceptedFileTypes = [])
    {
        if (empty($fileType) && !empty($this->defaultConsumes)) {
            return $this->getDI()->get($this->base . $this->defaultConsumes);
        } else {
            return $this->getExtension($fileType, $acceptedFileTypes);
        }
    }

    public function getResponseExtension($fileType = '', $acceptedFileTypes = [])
    {
        if (empty($fileType) && !empty($this->defaultProduces)) {
            return $this->getDI()->get($this->base . $this->defaultProduces);
        } else {
            return $this->getExtension($fileType, $acceptedFileTypes);
        }
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
        throw new \ApiBird\UnsupportedMediaTypeException();
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
     * @return \ApiBird\ExtensionProvider
     */
    public function setDefaultConsumes($type)
    {
        $this->defaultConsumes = $type;
        return $this;
    }

    /**
     * Set default Produces type
     * @param string $type
     * @return \ApiBird\ExtensionProvider
     */
    public function setDefaultProduces($type)
    {
        $this->defaultProduces = $type;
        return $this;
    }

}
