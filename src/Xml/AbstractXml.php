<?php

namespace Norgul\Xmpp\Xml;

use DOMDocument;

abstract class AbstractXml
{
    /**
     * @var $instance DOMDocument
     */
    protected $instance = null;

    public function __construct()
    {
        $this->instance = new DOMDocument();
        $this->instance->formatOutput = true;
    }
}