<?php

namespace Norgul\Xmpp;

use Norgul\Xmpp\Buffers\Response;
use Norgul\Xmpp\Exceptions\StreamError;
use Norgul\Xmpp\Xml\Stanzas\Auth;
use Norgul\Xmpp\Xml\Stanzas\Iq;
use Norgul\Xmpp\Xml\Stanzas\Message;
use Norgul\Xmpp\Xml\Stanzas\Presence;
use Norgul\Xmpp\Xml\Xml;

class XmppClient
{
    use Xml;

    protected $socket;
    protected $options;
    protected $responseBuffer;

    public $auth;
    public $iq;
    public $presence;
    public $message;

    public function __construct(Options $options, $sessionId = null)
    {
        $this->options = $options;
        $this->responseBuffer = new Response();

        try {
            $this->socket = new Socket($options, $this->responseBuffer);
        } catch (Exceptions\DeadSocket $e) {
            $this->options->getLogger()->error(__METHOD__ . '::' . __LINE__ . " " . $e->getMessage());
            return;
        }

        $this->auth = new Auth($this->socket);
        $this->iq = new Iq($this->socket);
        $this->presence = new Presence($this->socket);
        $this->message = new Message($this->socket);

        if ($this->options->getSessionManager() !== false) {
            $this->initSession($sessionId);
        }
    }

    public function connect()
    {
        $this->openStream();
        $this->auth->authenticate();
        $this->iq->setResource($this->options->getResource());
        $this->sendInitialPresenceStanza();
    }

    public function send(string $xml)
    {
        $this->socket->send($xml);
    }

    public function getResponse(): string
    {
        $this->socket->receive();
        $response = $this->responseBuffer->read();
        $finalResponse = $this->checkForErrors($response);

        return $finalResponse;
    }

    /**
     * Extracting messages from the response
     * @return array
     */
    public function getMessages(): array
    {
        return $this->message->receive();
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
        $this->socket->send(self::closeXmlStream());
        $this->socket->disconnect();
    }

    protected function openStream()
    {
        $openStreamXml = self::openXmlStream($this->options->getHost());
        $this->socket->send($openStreamXml);
    }

    protected function sendInitialPresenceStanza()
    {
        $this->socket->send('<presence/>');
    }

    protected function initSession($sessionId)
    {
        session_id($sessionId ?: uniqid());
        session_start();
    }

    protected function checkForErrors(string $response): string
    {
        try {
            self::hasUnrecoverableErrors($response);
        } catch (StreamError $e) {
            $this->options->getLogger()->logResponse(__METHOD__ . '::' . __LINE__ . " $response");
            $this->options->getLogger()->error(__METHOD__ . '::' . __LINE__ . " " . $e->getMessage());
            $this->connect();
            $response = '';
        }
        return $response;
    }
}
