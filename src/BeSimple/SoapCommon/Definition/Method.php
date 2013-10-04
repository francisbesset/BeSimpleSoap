<?php

namespace BeSimple\SoapServer\Definition;

class Method
{
    private $name;
    private $headers;
    private $input;
    private $output;
    private $fault;

    public function __construct($name)
    {
        $this->name = $name;
        $this->header = new Message($name.'Header');
        $this->input = new Message($name.'Request');
        $this->output = new Message($name.'Response');
        $this->fault = new Message($name.'Fault');
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVersions()
    {
        return array(SOAP_1_1, SOAP_1_2);
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getFault()
    {
        return $this->fault;
    }
}
