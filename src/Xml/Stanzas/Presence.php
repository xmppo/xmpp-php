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
        $this->sendXml("<presence from=\"{$this->options->bareJid()}\" to=\"{$to}\" type=\"{$type}\"/>");
    }

    /**
     * Set priority to current resource by default, or optional other resource tied to the
     * current username
     * @param int $value
     * @param string|null $forResource
     */
    public function setPriority(int $value, string $forResource = null)
    {
        if (!$forResource) {
            $from = self::quote($this->options->fullJid());
        } else {
            $from = $this->options->getUsername() . "/$forResource";
        }

        $this->sendXml("<presence from=\"{$from}\">
            <priority>{$this->limitPriority($value)}</priority></presence>");
    }

    protected function limitPriority(int $value): int
    {
        if ($value > self::PRIORITY_UPPER_BOUND) {
            return self::PRIORITY_UPPER_BOUND;
        } elseif ($value < self::PRIORITY_LOWER_BOUND) {
            return self::PRIORITY_LOWER_BOUND;
        } else {
            return $value;
        }
    }
}
