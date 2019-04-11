<?php

namespace Norgul\Xmpp\Authentication\AuthTypes;

class Plain extends Authentication
{
    protected $name = 'PLAIN';

    public function encodedCredentials(): string
    {
        $credentials = "\x00{$this->options->getUsername()}\x00{$this->options->getPassword()}";
        return self::quote(base64_encode($credentials));
    }
}
