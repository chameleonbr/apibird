<?php

namespace ApiBird;

class ExtensionProvider extends \Phalcon\DI\Injectable
{

    protected $base = 'apibird.';
    protected $extensions = array();
    protected $defaultContentType = null;
    protected $defaultAccept = null;

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
            });
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
        if (empty($fileType) && !empty($this->defaultContentType)) {
            return $this->getDI()->get($this->base . $this->defaultContentType);
        } else {
            return $this->getExtension($fileType, $acceptedFileTypes);
        }
    }

    public function getResponseExtension($fileType = '', $acceptedFileTypes = [])
    {
        if (empty($fileType) && !empty($this->defaultAccept)) {
            return $this->getDI()->get($this->base . $this->defaultAccept);
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
    public function getExtension($fileType = '', $acceptedFileTypes = [])
    {
        if (isset($this->extensions[$fileType])) {
            if (!empty($acceptedFileTypes) && in_array($this->extensions[$fileType], $acceptedFileTypes)) {
                return $this->getDI()->get($this->base . $this->extensions[$fileType]);
            } else {
                return $this->getDI()->get($this->base . $this->extensions[$fileType]);
            }
        }
        throw new \ApiBird\InvalidTypeException('Unsupported Media Type', 415, $this);
    }

    public function hasExtension($fileType = '', $acceptedFileTypes = [])
    {
        if (isset($this->extensions[$fileType]) &&
                in_array($this->extensions[$fileType], $acceptedFileTypes)) {
            return true;
        }
        return false;
    }

    public function hasRequestExtension($fileType = '', $acceptedFileTypes = [])
    {
        $di = $this->getDI();
        if ((empty($fileType) || $fileType == '*/*') && $di->has($this->base . $this->defaultContentType)) {
            return true;
        } elseif (isset($this->extensions[$fileType]) &&
                in_array($this->extensions[$fileType], $acceptedFileTypes)) {
            return true;
        }
        return false;
    }

    public function hasResponseExtension($fileType = '', $acceptedFileTypes = [])
    {
        $di = $this->getDI();
        if ((empty($fileType) || $fileType == '*/*') && $di->has($this->base . $this->defaultAccept)) {
            return true;
        } elseif (isset($this->extensions[$fileType]) &&
                in_array($this->extensions[$fileType], $acceptedFileTypes)) {
            return true;
        }
        return false;
    }

    public function setDefaultAccept($type)
    {
        $this->defaultAccept = $type;
        return $this;
    }

    public function setDefaultContentType($type)
    {
        $this->defaultContentType = $type;
        return $this;
    }

}
