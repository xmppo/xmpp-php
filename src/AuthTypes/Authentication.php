<?php

namespace Norgul\Xmpp\AuthTypes;

use Norgul\Xmpp\Options;
use Norgul\Xmpp\Xml\Xml;

abstract class Authentication implements Authenticable
{
    use Xml;

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
