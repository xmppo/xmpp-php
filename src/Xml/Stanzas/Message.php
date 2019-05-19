<?php

namespace Norgul\Xmpp\Xml\Stanzas;

class Message extends Stanza
{
    public function send(string $body, string $to, string $type = "chat")
    {
        $to = self::quote($to);
        $body = self::quote($body);

        $bodyXml = "<body>{$body}</body>";
        $xml = "<message to='{$to}' type='{$type}'>{$bodyXml}</message>";

        $this->socket->send($xml);
    }

    public function receive()
    {
        return self::parseTag($this->socket->getResponseBuffer()->read(), "message");
    }
}
