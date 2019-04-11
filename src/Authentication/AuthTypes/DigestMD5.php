<?php

namespace Norgul\Xmpp\Authentication\AuthTypes;

class DigestMD5 extends Authentication
{
    protected $name = 'DIGEST-MD5';

    public function encodedCredentials(): string
    {
        $credentials = "\x00{$this->options->getUsername()}\x00{$this->options->getPassword()}";
        return self::quote(sha1($credentials));
    }
}