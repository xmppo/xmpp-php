<?php

namespace Norgul\Xmpp\Xml\Stanzas;

use Norgul\Xmpp\Xml\AbstractXml;

class Presence extends AbstractXml
{
    protected $xmlRootName = 'presence';

    public function requestPresence(string $from, string $to, string $type = "subscribe"): string
    {
        $root = $this->instance->createElement($this->xmlRootName);
        $root->setAttribute("from", $from);
        $root->setAttribute("to", $to);
        $root->setAttribute("type", $type);

        return $this->instance->saveXML($root);
    }

    public function setPriority(int $priority, string $from = null): string
    {
        $root = $this->instance->createElement($this->xmlRootName);

        /**
         * XMPP priority limitations
         */
        if ($priority > 127)
            $priority = 127;
        else if ($priority < -128)
            $priority = -128;

        if ($from)
            $root->setAttribute("from", $from);

        $priorityNode = $this->instance->createElement('priority', $priority);

        $root->appendChild($priorityNode);

        return $this->instance->saveXML($root);
    }
}