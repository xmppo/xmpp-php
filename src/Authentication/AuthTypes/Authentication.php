<?php

namespace Norgul\Xmpp\Authentication\AuthTypes;

use Norgul\Xmpp\Options;

abstract class Authentication implements Authenticable
{
    protected $name;
    protected $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }
}