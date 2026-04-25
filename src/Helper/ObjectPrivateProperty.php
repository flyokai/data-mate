<?php

namespace Flyokai\DataMate\Helper;

class ObjectPrivateProperty
{
    public function set($obj, $prop, $value, $class = null)
    {
        if ($class == null) $class = get_class($obj);
        $parentClass = get_parent_class($class);
        $isWritable = false;
        try {
            $this->_get($obj, $prop, $class, true);
            $cacheKey = $this->cacheKey($obj, $class, $prop, true);
            $write = &$this->readerCache[$cacheKey]($obj, $class, $prop, true);
            $isWritable = true;
        } catch (\InvalidArgumentException $e) {
            if ($parentClass) {
                try {
                    $this->_get($obj, $prop, $parentClass, true);
                    $cacheKey = $this->cacheKey($obj, $parentClass, $prop, true);
                    $write = &$this->readerCache[$cacheKey]($obj, $parentClass, $prop, true);
                    $isWritable = true;
                } catch (\InvalidArgumentException $e) {

                }
            }
        }
        if (!$isWritable) {
            $this->_get($obj, $prop, $class, false);
            $cacheKey = $this->cacheKey($obj, $class, $prop, false);
            $write = &$this->readerCache[$cacheKey]($obj, $class, $prop, false);
        }
        $write = $value;
        return $this;
    }

    public function get($obj, $prop, $class = null)
    {
        if ($class == null) $class = get_class($obj);
        $parentClass = get_parent_class($class);
        $propValue = null;
        try {
            $propValue = $this->_get($obj, $prop, $class, true);
        } catch (\InvalidArgumentException $e) {
            if ($parentClass) {
                try {
                    $propValue = $this->_get($obj, $prop, $parentClass, true);
                } catch (\InvalidArgumentException $e) {}
            }
        }
        return $propValue;
    }
    protected function _get($obj, $prop, $class, $rise)
    {
        $cacheKey = $this->cacheKey($obj, $class, $prop, $rise);
        $this->initReader($obj, $class, $prop, $rise);
        return $this->readerCache[$cacheKey]($obj, $class, $prop, $rise);
    }

    protected $readerCache = [];

    protected function cacheKey($obj, $class, $prop, $rise)
    {
        return spl_object_hash($obj) . '::' . $class . '::' . $prop . '::' . $rise;
    }

    protected function initReader($obj, $class, $prop, $rise)
    {
        $cacheKey = $this->cacheKey($obj, $class, $prop, $rise);
        if (!isset($this->readerCache[$cacheKey])) {
            $this->readerCache[$cacheKey] = function & ($object, $class, $property, $rise) {
                \set_error_handler(static fn () => true);
                try {
                $value = &\Closure::bind(function & () use ($property,$rise) {
                    if (!property_exists($this, $property)) {
                        if ($rise) {
                            throw new \InvalidArgumentException($property . ' property does not exist in ' . get_class($this) . ' class');
                        } else {
                           @$this->$property = null;
                        }
                    }
                    return $this->$property;
                }, $object, $class)->__invoke();
                } finally {
                    \restore_error_handler();
                }
                return $value;
            };
        }
        return $this;
    }
}
