<?php

namespace BeSimple\SoapWsdl;

use BeSimple\SoapServer\Definition\Definition;
use BeSimple\SoapServer\Definition\Method;

class Dumper
{
    const XML_NS = 'xmlns';
    const XML_NS_URI = 'http://www.w3.org/2000/xmlns/';

    const WSDL_NS = 'wsdl';
    const WSDL_NS_URI = 'http://schemas.xmlsoap.org/wsdl/';

    const SOAP_NS = 'soap';
    const SOAP_NS_URI = 'http://schemas.xmlsoap.org/wsdl/soap/';

    const SOAP12_NS = 'soap12';
    const SOAP12_NS_URI = 'http://schemas.xmlsoap.org/wsdl/soap12/';

    const SOAP_ENC_NS = 'soap-enc';
    const SOAP_ENC_URI = 'http://schemas.xmlsoap.org/soap/encoding/';

    const XSD_NS = 'xsd';
    const XSD_NS_URI = 'http://www.w3.org/2001/XMLSchema';

    const TYPES_NS = 'tns';

    protected $definition;
    protected $document;

    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }

    public function dump()
    {
        $this->createDocument();

        $definitions = $this->addDefinitions();
        $this->addOperations($definitions);

        return $this->document->saveXML();
    }

    protected function createDocument()
    {
        $this->document = new \DOMDocument('1.0', 'utf-8');

        return $this;
    }

    protected function addDefinitions()
    {
        $definitions = $this->document->createElement('definitions');
        $definitions->setAttributeNS(static::XML_NS_URI, static::XML_NS, static::WSDL_NS_URI);
        $definitions->setAttributeNS(static::XML_NS_URI, static::XML_NS.':'.static::TYPES_NS, $this->definition->getNamespace());
        $definitions->setAttributeNS(static::XML_NS_URI, static::XML_NS.':'.static::SOAP_NS, static::SOAP_NS_URI);
        $definitions->setAttributeNS(static::XML_NS_URI, static::XML_NS.':'.static::XSD_NS, static::XSD_NS_URI);
        $definitions->setAttributeNS(static::XML_NS_URI, static::XML_NS.':'.static::SOAP_ENC_NS, static::SOAP_ENC_URI);
        $definitions->setAttributeNS(static::XML_NS_URI, static::XML_NS.':'.static::WSDL_NS, static::WSDL_NS_URI);

        $definitions->setAttribute('name', $this->definition->getName());
        $definitions->setAttribute('targetNamespace', $this->definition->getNamespace());

        $version = $this->definition->getOption('version');
        if (SOAP_1_2 === $version) {
            $definitions->setAttribute(static::XML_NS.':'.static::SOAP12_NS, static::SOAP12_NS_URI);
        }

        $this->document->appendChild($definitions);

        return $definitions;
    }

    protected function addOperations(\DOMElement $definitions)
    {
        $portType = $this->addPortType($definitions);
        foreach ($this->definition->getMethods() as $method) {
            $this->addMethod($method, $portType);

            //$this->addPortTypeOperatin($portType, $operation);

            foreach ($method->getVersions() as $version) {
                //$this->addOperationVersion($definitions, $operation, $version);
            }
        }
    }

    protected function addMethod(Method $method, \DOMElement $portType)
    {
        $this->addPortOperation($portType, $method);

        foreach ($method->getVersions() as $version) {
            //$this->addMethodVersion($method, $version);
        }
    }

    protected function addMethodVersion(Method $method, $version)
    {
        $soapNs = SOAP_1_1 == $version ? static::SOAP_NS : static::SOAP12_NS;
    }

    protected function addOperation(\DOMElement $definitions, Operation $operation, $version)
    {
    }

    protected function addPortType(\DOMElement $definitions/*, $name*/)
    {
        $portType = $this->document->createElement('portType');
        $portType->setAttribute('name', $this->definition->getName().'PortType');

        $definitions->appendChild($portType);

        return $portType;
    }

    protected function addPortOperation(\DOMElement $portType, Method $method)
    {
        $operation = $this->document->createElement('operation');
        $operation->setAttribute('name', $method->getName());

        foreach (array('input' => $method->getInput(), 'output' => $method->getOutput(), 'fault' => $method->getFault()) as $type => $message) {
            if (!$message->hasPart()) {
                continue;
            }

            $node = $this->document->createElement($type);
            $node->setAttribute('message', static::TYPES_NS.':'.$message->getName());

            $operation->appendChild($node);
        }

        $portType->appendChild($operation);

        return $operation;
    }
}
