<?php

namespace Norgul\Xmpp\Authorization;

use Norgul\Xmpp\Xml;

class Plain implements AuthInterface
{
    private $name = 'PLAIN';

    public static function encodedCredentials($username, $password)
    {
        return XML::quote(base64_encode("\x00" . $username . "\x00" . $password));
    }

    public function getName(): string
    {
        return $this->name;
    }

}