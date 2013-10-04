<?php

namespace BeSimple\SoapServer\Definition;

class Definition
{
    protected $options;
    protected $methods;

    public function __construct($name, $namespace, array $options = array())
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->methods = array();

        $this->setOptions($options);
    }

    public function setOptions(array $options)
    {
        $this->options = array(
            'version' => SOAP_1_1,
            'style' => SOAP_RPC,
            'use' => SOAP_LITERAL,
        );

        $invalid = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }

        if ($invalid) {
            throw new \InvalidArgumentException(sprintf('The Definition does not support the following options: "%s"', implode('", "', $invalid)));
        }

        return $this;
    }

    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Definition does not support the "%s" option.', $key));
        }

        $this->options[$key] = $value;

        return $this;
    }

    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Definition does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function addMethod(Method $method, $version = null)
    {
        if (isset($this->methods[$method->getName()])) {
            throw new \Exception(sprintf('The method "%s" already exists', $method->getName()));
        }

        $this->methods[$method->getName()] = $method;

        return $this;
    }
}
