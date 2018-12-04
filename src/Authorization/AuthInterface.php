<?php

namespace Norgul\Xmpp\Authorization;

interface AuthInterface
{
    /**
     * Based on auth type, return the right format of credentials to be sent to the server
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    public static function encodedCredentials($username, $password);

    /**
     * Simple name getter
     *
     * @return string
     */
    public function getName(): string;
}