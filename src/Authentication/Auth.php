<?php

namespace Norgul\Xmpp\Authentication;

use Norgul\Xmpp\Options;

class Auth
{
    protected $authType;
    protected $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function authenticate()
    {
        $mechanism = $this->options->getAuthType()->getName();
        $encodedCredentials = $this->options->getAuthType()->encodedCredentials();
        $nameSpace = "urn:ietf:params:xml:ns:xmpp-sasl";

        return "<auth xmlns='{$nameSpace}' mechanism='{$mechanism}'>{$encodedCredentials}</auth>";
    }
}
