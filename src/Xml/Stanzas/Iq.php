<?php

namespace Norgul\Xmpp\Xml\Stanzas;

class Iq extends Stanza
{
    public function getRoster()
    {
        $this->sendXml("<iq type=\"get\" id=\"{$this->uniqueId()}\"><query xmlns=\"jabber:iq:roster\"/></iq>");
    }

    /**
     * Add JID to roster and give him your hand picked name. Adding to group is optional
     *
     * @param string $name
     * @param string $forJid
     * @param string $from
     * @param string|null $groupName
     */
    public function addToRoster(string $name, string $forJid, string $from, string $groupName = null)
    {
        $group = $groupName ? "<group>{$groupName}</group>" : null;
        $this->sendXml("<iq type=\"set\" id=\"{$this->uniqueId()}\" from=\"{$from}\">
            <query xmlns=\"jabber:iq:roster\">
            <item jid=\"{$forJid}\" name=\"{$name}\">
            {$group}</item></query></iq>");
    }

    public function removeFromRoster(string $jid, string $myJid)
    {
        $this->sendXml("<iq type=\"set\" id=\"{$this->uniqueId()}\" from=\"{$myJid}\">
            <query xmlns=\"jabber:iq:roster\">
            <item jid=\"{$jid}\" subscription=\"remove\"/>
            </query></iq>");
    }

    public function setResource(string $name)
    {
        if (!trim($name))
            return;

        $this->sendXml("<iq type=\"set\" id={$this->uniqueId()}>
            <bind xmlns=\"urn:ietf:params:xml:ns:xmpp-bind\">
            <resource>{$name}</resource>
            </bind></iq>");
    }

    public function setGroup(string $name, string $forJid)
    {
        $this->sendXml("<iq type=\"set\" id=\"{$this->uniqueId()}\">
            <query xmlns=\"jabber:iq:roster\"><item jid=\"{$forJid}\">
            <group>{$name}</group></item></query></iq>");
    }

    public function getServerVersion()
    {
        $this->sendXml("<iq type=\"get\" id=\"{$this->uniqueId()}\">
            <query xmlns=\"jabber:iq:version\"/></iq>");
    }

    public function getServerFeatures()
    {
        $this->sendXml("<iq type=\"get\" id=\"{$this->uniqueId()}\">
            <query xmlns=\"http://jabber.org/protocol/disco#info\"></query></iq>");
    }

    public function getServerTime()
    {
        $this->sendXml("<iq type=\"get\" id=\"{$this->uniqueId()}\">
            <query xmlns=\"urn:xmpp:time\"/></iq>");
    }

    public function getFeatures(string $forJid)
    {
        $this->sendXml("<iq type=\"get\" to=\"{$forJid}\">
            <query xmlns=\"http://jabber.org/protocol/disco#info\"></query></iq>");
    }

    public function ping()
    {
        $this->sendXml("<iq type=\"get\" id=\"{$this->uniqueId()}\">
            <query xmlns=\"urn:xmpp:ping\"/></iq>");
    }
}