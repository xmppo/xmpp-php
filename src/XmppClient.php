<?php

namespace Norgul\Xmpp;

use Norgul\Xmpp\Authentication\Auth;
use Norgul\Xmpp\Xml\Parser;
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
    use Parser;

    protected $socket;
    protected $options;
    protected $auth;

    public $iq;
    public $presence;
    public $message;

    public $xmlSession;

    public function __construct(Options $options)
    {
        $this->options = $options;

        try {
            $this->socket = new Socket($options->fullSocketAddress());
        } catch (Exceptions\DeadSocket $e) {
            echo $e->getMessage();
            return;
        }

        $this->iq = new Iq($this->socket, $options);
        $this->presence = new Presence($this->socket, $options);
        $this->message = new Message($this->socket, $options);

        $this->xmlSession = fopen('response.xml', 'r');
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
        $this->options->getLogger()->info("REQUEST::" . __METHOD__ . '::' . __LINE__ . $xml);
    }

    public function getResponse(): string
    {
        $response = '';
        while ($out = fgets($this->socket->connection)) {
            $response .= $out;
        }

        return $response;
    }

    public function prettyPrint($response)
    {
        if (!$response) {
            return;
        }

        $separator = "\n-------------\n";
        echo "{$separator} $response {$separator}";
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
        $this->responseLog();

        if (self::isTlsRequired($this->readFile()) && $this->options->usingTls()) {
            $this->startTls();
        }

        $this->auth = new Auth($this->options);

        $this->send($this->auth->authenticate());
        $this->openStream();
    }

    protected function startTls()
    {
        $this->send("<starttls xmlns='urn:ietf:params:xml:ns:xmpp-tls'/>");

        $this->responseLog();

        if (!self::canProceed($this->readFile())) {
            $this->options->getLogger()->error("TLS authentication failed. 
            Trying to continue but will most likely fail.");
        }

        stream_socket_enable_crypto($this->socket->connection, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);
        $this->openStream();
    }

    protected function openStream()
    {
        $this->send(self::openXmlStream($this->options->getHost()));
        $this->responseLog();
    }

    protected function sendInitialPresenceStanza()
    {
        $this->send('<presence/>');
    }

    protected function responseLog()
    {
        $response = $this->getResponse();
        $this->options->getLogger()->info("RESPONSE::" . __METHOD__ . '::' . __LINE__ . $response);
        return $response;
    }

    protected function readFile()
    {
        return fread($this->xmlSession, filesize('response.xml'));
    }
}
