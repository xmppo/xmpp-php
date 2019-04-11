<?php

namespace Norgul\Xmpp\Xml\Stanzas;

use Norgul\Xmpp\Options;
use Norgul\Xmpp\Socket;
use Norgul\Xmpp\Xml\Xml;

abstract class Stanza
{
    use Xml;

    protected $socket;
    protected $options;

    public function __construct(Socket $socket, Options $options)
    {
        $this->socket = $socket;
        $this->options = $options;
    }

    protected function sendXml(string $xml)
    {
        $this->socket->send($xml);
    }

    protected function uniqueId(): string
    {
        return uniqid();
    }
}