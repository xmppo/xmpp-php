<?php

namespace Norgul\Xmpp;

use Norgul\Xmpp\Xml\Stanzas\Auth;
use Norgul\Xmpp\Xml\Stanzas\Iq;
use Norgul\Xmpp\Xml\Stanzas\Message;
use Norgul\Xmpp\Xml\Stanzas\Presence;
use Norgul\Xmpp\Xml\Xml;

/**
 * Class Socket
 * @package Norgul\Xmpp
 */
class XmppClient
{
    use Xml;

    protected $socket;
    protected $options;

    public $iq;
    public $presence;
    public $message;

    public function __construct(Options $options)
    {
        $this->options = $options;

        try {
            $this->socket = new Socket($options);
        } catch (Exceptions\DeadSocket $e) {
            echo $e->getMessage();
            return;
        }

        $this->iq = new Iq($this->socket, $options);
        $this->presence = new Presence($this->socket, $options);
        $this->message = new Message($this->socket, $options);
    }

    public function connect()
    {
        $this->openStream();
        $this->authenticate();
        $this->iq->setResource($this->options->getResource());
        $this->sendInitialPresenceStanza();
    }

    public function send(string $xml)
    {
        $this->socket->send($xml);
    }

    public function getResponse(): string
    {
        return $this->socket->receive();
    }

    /**
     * Extracting messages from the response
     * @return array
     */
    public function getMessages(): array
    {
        return self::parseTag($this->getResponse(), "message");
    }

    public function prettyPrint($response)
    {
        if (!$response) {
            return;
        }

        $separator = "\n-------------\n";
        echo "{$separator} $response {$separator}";
    }

    public function disconnect()
    {
        $this->socket->autoAnswerSend(self::closeXmlStream());
        $this->socket->disconnect();
    }

    protected function openStream()
    {
        $openStreamXml = self::openXmlStream($this->options->getHost());
        $this->socket->autoAnswerSend($openStreamXml);
    }

    protected function authenticate()
    {
        $auth = new Auth($this->socket, $this->options);
        $auth->authenticate();
    }

    protected function sendInitialPresenceStanza()
    {
        $this->socket->autoAnswerSend('<presence/>');
    }
}
