<?php

namespace App\Tools;

/**
 * Wraps an object and makes all its methods and properties accessible.
 */
class Proxy
{
    private object $object;
    private \ReflectionObject $reflection;

    public function __construct(object $object)
    {
        $this->object = $object;
        $this->reflection = new \ReflectionObject($object);
    }

    public function __get($attribute)
    {
        $reflection = $this->findPropertyReflection($this->reflection, $attribute);

        return $reflection?->getValue($this->object);
    }

    public function __set($attribute, $value)
    {
        $prop = $this->findPropertyReflection($this->reflection, $attribute);

        if (!$prop) {
            return $this;
        }

        $prop->setValue($this->object, $value);

        return $this;
    }

    public function __call($method, array $args)
    {
        $reflection = $this->findMethodReflection($this->reflection, $method);

        if (!$reflection) {
            return null;
        }

        $params = [];
        foreach ($args as &$arg) {
            $params[] = &$arg;
        }

        return $reflection->invokeArgs($this->object, $params);
    }

    private function findPropertyReflection(\ReflectionClass $reflectionClass, string $attribute)
    {
        if ($reflectionClass->hasProperty($attribute)) {
            return $reflectionClass->getProperty($attribute);
        }

        if ($reflectionClass->getParentClass()) {
            return $this->findPropertyReflection($reflectionClass->getParentClass(), $attribute);
        }

        return null;
    }

    private function findMethodReflection(\ReflectionClass $reflectionClass, string $method)
    {
        if ($reflectionClass->hasMethod($method)) {
            return $reflectionClass->getMethod($method);
        }

        if ($reflectionClass->getParentClass()) {
            return $this->findMethodReflection($reflectionClass->getParentClass(), $method);
        }

        return null;
    }
}
