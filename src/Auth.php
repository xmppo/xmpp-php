<?php

namespace Norgul\Xmpp;

use Norgul\Xmpp\Authorization\AuthInterface;

class Auth
{
    /**
     * Construct XML string to include credentials hashed based on AuthInterface type:
     * PLAIN, DIGEST-MD5...
     *
     * @param AuthInterface $authType
     * @param $username
     * @param $password
     * @return mixed
     */
    public static function authorize(AuthInterface $authType, $username, $password)
    {
        $encodedCredentials = $authType::encodedCredentials($username, $password);
        return str_replace(['{mechanism}' ,'{encoded}'], [$authType->getName(), $encodedCredentials], XML::AUTH);
    }



}