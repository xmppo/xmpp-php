<?php

namespace Norgul\Xmpp;

use Norgul\Xmpp\Authentication\Auth;
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
    /**
     * @var Socket $socket
     */
    protected $socket;
    protected $options;

    protected $auth;

    public $iq;
    public $presence;
    public $message;

    public function __construct(Options $options)
    {
        $this->options = $options;
        $this->socket = new Socket($options->fullSocketAddress());
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

    public function disconnect()
    {
        $this->send(self::closeXmlStream());
        fclose($this->socket->connection);
    }

    public function send(string $xml)
    {
        $this->socket->send($xml);
    }

    /**
     * Get response from server if any.
     * @param bool $echoOutput
     * @return string
     */
    public function getResponse($echoOutput = false): string
    {
        $response = '';
        while ($out = fgets($this->socket->connection)) {
            $response .= $out;
        }

        if ($echoOutput && $response) {
            $separator = "\n-------------\n";
            echo "{$separator} $response {$separator}";
        }

        return $response;
    }

    /**
     * Extracting messages from the response
     * @return array
     */
    public function getMessages(): array
    {
        return self::parseTag($this->getResponse(), "message");
    }

    protected function authenticate()
    {
        $this->auth = new Auth($this->options);
        $this->send($this->auth->authenticate());
    }

    protected function openStream()
    {
        $this->send(self::openXmlStream($this->options->getHost()));
    }

    protected function sendInitialPresenceStanza()
    {
        $this->send('<presence/>');
    }
}