<?php

namespace Norgul\Xmpp\Xml\Stanzas;

class Presence extends Stanza
{
    const PRIORITY_UPPER_BOUND = 127;
    const PRIORITY_LOWER_BOUND = -128;

    public function subscribe(string $to)
    {
        $this->setPresence($to, 'subscribe');
    }

    public function unsubscribe(string $from)
    {
        $this->setPresence($from, 'unsubscribe');
    }

    public function acceptSubscription(string $from)
    {
        $this->setPresence($from, 'subscribed');
    }

    public function declineSubscription(string $from)
    {
        $this->setPresence($from, 'unsubscribed');
    }

    protected function setPresence(string $to, string $type = "subscribe")
    {
        $xml = "<presence from='{$this->socket->getOptions()->bareJid()}' to='{$to}' type='{$type}'/>";
        $this->socket->send($xml);
    }

    /**
     * Set priority to current resource by default, or optional other resource tied to the
     * current username
     * @param int $value
     * @param string|null $forResource
     */
    public function setPriority(int $value, string $forResource = null)
    {
        $from = self::quote($this->socket->getOptions()->fullJid());

        if ($forResource) {
            $from = $this->socket->getOptions()->getUsername() . "/$forResource";
        }

        $priority = "<priority>{$this->limitPriority($value)}</priority>";
        $xml = "<presence from='{$from}'>{$priority}</presence>";

        $this->socket->send($xml);
    }

    protected function limitPriority(int $value): int
    {
        if ($value > self::PRIORITY_UPPER_BOUND) {
            return self::PRIORITY_UPPER_BOUND;
        } elseif ($value < self::PRIORITY_LOWER_BOUND) {
            return self::PRIORITY_LOWER_BOUND;
        }

        return $value;
    }
}
