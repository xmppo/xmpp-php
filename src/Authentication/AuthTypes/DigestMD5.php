<?php

namespace Norgul\Xmpp\Authentication\AuthTypes;

use Norgul\Xmpp\Options;
use Norgul\Xmpp\Xml\Xml;

class DigestMD5 implements Authenticable
{
    protected $name = 'DIGEST-MD5';
    protected $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function encodedCredentials(): string
    {
        $credentials = "\x00{$this->options->getUsername()}\x00{$this->options->getPassword()}";
        return XML::quote(sha1($credentials));
    }
}