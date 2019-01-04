<?php


namespace Norgul\Xmpp\Xml;


class Auth extends AbstractXml
{
    protected $xmlRootName = 'auth';

    public function authenticate(string $credentials, string $mechanism): string
    {
        $root = $this->instance->createElement($this->xmlRootName);

        $root->setAttribute('xmlns', 'urn:ietf:params:xml:ns:xmpp-sasl');
        $root->setAttribute('mechanism', $mechanism);

        $credentialsNode = $this->instance->createTextNode($credentials);

        $root->appendChild($credentialsNode);

        return $this->instance->saveXML($root);
    }
}