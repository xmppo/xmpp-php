<?php

namespace Norgul\Xmpp\AuthTypes;

interface Authenticable
{
    /**
     * Based on auth type, return the right format of credentials to be sent to the server
     */
    public function encodedCredentials(): string;

    public function getName(): string;
}
