<?php

namespace Norgul\Xmpp\Xml\Stanzas;

class Message extends Stanza
{
    public function send(string $body, string $to, string $type = "chat")
    {
        $xml = $this->generateMessageXml($body, $to, $type);
        $this->socket->send($xml);
    }

    public function receive()
    {
        $this->socket->receive();
        return self::parseTag($this->socket->getResponseBuffer()->read(), "message");
    }

    protected function generateMessageXml(string $body, string $to, string $type): string
    {
        $to = self::quote($to);
        $body = self::quote($body);

        $bodyXml = "<body>{$body}</body>";

        return "<message to='{$to}' type='{$type}'>{$bodyXml}</message>";
    }
}
