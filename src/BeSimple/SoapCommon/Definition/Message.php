<?php

namespace BeSimple\SoapServer\Definition;

class Message
{
    const TYPE_HEADER = 1;
    const TYPE_REQUEST = 2;
    const TYPE_RESPONSE = 3;

    protected $name;
    protected $parts;

    public function __construct($name)
    {
        $this->name = $name;
        $this->parts = array();
    }

    public function getName()
    {
        return $this->name;
    }

    public function hasPart()
    {
        return 0 === count($this->parts) ? false : true;
    }

    public function addPart($name, $type)
    {
        if (isset($this->parts[$name])) {
            throw new \Exception();
        }

        $this->parts[$name] = new Part($name, $type);

        return $this;
    }
}
