<?php

namespace Norgul\Xmpp\Authentication\AuthTypes;

use Norgul\Xmpp\Xml\Xml;

class Plain extends Authentication
{
    protected $name = 'PLAIN';

    public function encodedCredentials(): string
    {
        $credentials = "\x00{$this->options->getUsername()}\x00{$this->options->getPassword()}";
        return XML::quote(base64_encode($credentials));
    }
}