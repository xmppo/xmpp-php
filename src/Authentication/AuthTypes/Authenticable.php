<?php

namespace Norgul\Xmpp\Authentication\AuthTypes;

interface Authenticable
{
    public function getName(): string;

    /**
     * Based on auth type, return the right format of credentials to be sent to the server
     */
    public function encodedCredentials(): string;
}
