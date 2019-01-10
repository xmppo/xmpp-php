<?php

namespace Norgul\Xmpp\Xml\Stanzas;

use Norgul\Xmpp\Xml\AbstractXml;

class Presence extends AbstractXml
{
    protected $xmlRootName = 'presence';

    public function setPresence(string $from, string $to, string $type = "subscribe"): string
    {
        $root = $this->instance->createElement($this->xmlRootName);
        $root->setAttribute("from", $from);
        $root->setAttribute("to", $to);
        $root->setAttribute("type", $type);

        echo $this->instance->saveXML($root);
        return $this->instance->saveXML($root);
    }

    public function setPriority(int $value, string $forResource = null): string
    {
        $root = $this->instance->createElement($this->xmlRootName);

        /**
         * XMPP priority limitations
         */
        if ($value > 127)
            $value = 127;
        else if ($value < -128)
            $value = -128;

        if ($forResource)
            $root->setAttribute("from", $forResource);

        $priorityNode = $this->instance->createElement('priority', $value);

        $root->appendChild($priorityNode);

        return $this->instance->saveXML($root);
    }
}