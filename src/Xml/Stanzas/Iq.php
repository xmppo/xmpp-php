<?php

namespace Norgul\Xmpp\Xml\Stanzas;

use Norgul\Xmpp\Xml\AbstractXml;

class Iq extends AbstractXml
{
    protected $xmlRootName = 'iq';

    public function getRoster(): string
    {
        $root = $this->instance->createElement($this->xmlRootName);
        $root->setAttribute('type', 'get');

        $queryNode = $this->instance->createElement('query');
        $queryNode->setAttribute('xmlns', 'jabber:iq:roster');

        $root->appendChild($queryNode);

        return $this->instance->saveXML($root);
    }

    public function setResource(string $resource): string
    {
        $root = $this->instance->createElement($this->xmlRootName);
        $root->setAttribute('type', 'set');

        $bindNode = $this->instance->createElement('bind');
        $bindNode->setAttribute('xmlns', 'urn:ietf:params:xml:ns:xmpp-bind');

        $resourceNode = $this->instance->createElement('resource', $resource);
        $bindNode->appendChild($resourceNode);

        $root->appendChild($bindNode);

        return $this->instance->saveXML($root);
    }

}