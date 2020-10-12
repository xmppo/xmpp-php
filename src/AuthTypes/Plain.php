<?php

namespace Norgul\Xmpp\AuthTypes;

class Plain extends Authentication
{
    protected $name = 'PLAIN';

    public function encodedCredentials(): string
    {
        $credentials = $this->options->getAuthZID()."\x00";
        $credentials .= $this->options->getUsername()."\x00";
        $credentials .= $this->options->getPassword();

        return self::quote(base64_encode($credentials));
    }
}
