<?php

namespace Norgul\Xmpp\AuthTypes;

class DigestMD5 extends Authentication
{
    protected $name = 'DIGEST-MD5';

    public function encodedCredentials(): string
    {
        $credentials = $this->options->getAuthZID()."\x00";
        $credentials .= $this->options->getUsername()."\x00";
        $credentials .= $this->options->getPassword();

        return self::quote(sha1($credentials));
    }
}
