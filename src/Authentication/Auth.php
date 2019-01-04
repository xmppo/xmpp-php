<?php

namespace Norgul\Xmpp\Authentication;

use Norgul\Xmpp\Authentication\AuthTypes\AuthTypeInterface;
use Norgul\Xmpp\Xml;

class Auth
{
    /**
     * Construct XML string to include credentials hashed based on AuthInterface type:
     * PLAIN, DIGEST-MD5...
     *
     * @param AuthTypeInterface $authType
     * @param $username
     * @param $password
     * @return mixed
     */
    public static function authenticate(AuthTypeInterface $authType, $username, $password)
    {
        $encodedCredentials = $authType::encodedCredentials($username, $password);

        return (new Xml\Auth())->authenticate($encodedCredentials, $authType->getName());
    }
}