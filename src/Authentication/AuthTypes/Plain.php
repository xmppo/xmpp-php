<?php

namespace Norgul\Xmpp\Authentication\AuthTypes;

use Norgul\Xmpp\Xml\Xml;

class Plain implements AuthTypeInterface
{
    private $name = 'PLAIN';

    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    public static function encodedCredentials(string $username, string $password): string
    {
        return XML::quote(base64_encode("\x00$username\x00$password"));
    }

    public function getName(): string
    {
        return $this->name;
    }
}