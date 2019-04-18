<?php

namespace Norgul\Xmpp\Authentication;

use Norgul\Xmpp\Authentication\AuthTypes\Authenticable;
use Norgul\Xmpp\Authentication\AuthTypes\Plain;
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
        $mechanism = $this->getAuthType()->getName();
        $encodedCredentials = $this->getAuthType()->encodedCredentials();
        $nameSpace = "urn:ietf:params:xml:ns:xmpp-sasl";

        return "<auth xmlns='{$nameSpace}' mechanism='{$mechanism}'>{$encodedCredentials}</auth>";
    }

    public function setAuthType(Authenticable $authType)
    {
        $this->authType = $authType;
        return $this;
    }

    public function getAuthType()
    {
        if (!$this->authType) {
            $this->authType = new Plain($this->options);
        }

        return $this->authType;
    }
}
