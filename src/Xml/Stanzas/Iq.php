<?php

namespace Norgul\Xmpp\Xml\Stanzas;

use Norgul\Xmpp\Xml\AbstractXml;

class Iq extends AbstractXml
{
    protected $xmlRootName = 'iq';

    public function getRoster(): string
    {
        $root = $this->setRoot('get');

        $queryNode = $this->instance->createElement('query');
        $queryNode->setAttribute('xmlns', 'jabber:iq:roster');

        $root->appendChild($queryNode);

        return $this->instance->saveXML($root);
    }

    public function setResource(string $name): string
    {
        $root = $this->setRoot('set');

        $bindNode = $this->instance->createElement('bind');
        $bindNode->setAttribute('xmlns', 'urn:ietf:params:xml:ns:xmpp-bind');

        $resourceNode = $this->instance->createElement('resource', $name);
        $bindNode->appendChild($resourceNode);

        $root->appendChild($bindNode);

        return $this->instance->saveXML($root);
    }

    public function setGroup(string $name, string $forJid)
    {
        $root = $this->setRoot('set');

        $queryNode = $this->instance->createElement('query');
        $queryNode->setAttribute('xmlns', 'jabber:iq:roster');

        $root->appendChild($queryNode);

        $itemNode = $this->instance->createElement('item');
        $itemNode->setAttribute('jid', $forJid);

        $queryNode->appendChild($itemNode);

        $groupNode = $this->instance->createElement('group', $name);

        $itemNode->appendChild($groupNode);

        return $this->instance->saveXML($root);
    }

    private function setRoot(string $type)
    {
        $root = $this->instance->createElement($this->xmlRootName);
        $root->setAttribute('type', $type);
        return $root;
    }

}