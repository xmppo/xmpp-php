<?php

namespace Norgul\Xmpp\Authentication\AuthTypes;

interface AuthTypeInterface
{
    /**
     * Based on auth type, return the right format of credentials to be sent to the server
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    public static function encodedCredentials(string $username, string $password): string;

    /**
     * Simple name getter
     *
     * @return string
     */
    public function getName(): string;
}