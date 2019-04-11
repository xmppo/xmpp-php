<?php

namespace Norgul\Xmpp\Xml\Stanzas;

class Message extends Stanza
{
    public function send(string $body, string $to, string $type = "chat")
    {
        $to = self::quote($to);
        $body = self::quote($body);
        $this->sendXml("<message to=\"{$to}\" type=\"{$type}\"><body>{$body}</body></message>");
    }
}
