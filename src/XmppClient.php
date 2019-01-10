<?php

namespace Norgul\Xmpp;

use Exception;
use Norgul\Xmpp\Authentication\Auth;
use Norgul\Xmpp\Authentication\AuthTypes\AuthTypeInterface;
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
    protected $socket;
    protected $options;

    private $iq;
    private $presence;
    private $message;

    /**
     * XmppClient constructor. Initializing a new socket
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
        $this->iq = new Iq();
        $this->presence = new Presence();
        $this->message = new Message();
    }

    /**
     * Open socket to host:port and authenticate with given credentials
     */
    public function connect()
    {
        $this->socket = stream_socket_client($this->options->fullSocketAddress());

        // Wait max 3 seconds by default before terminating the socket. Can be changed with options
        stream_set_timeout($this->socket, $this->options->getSocketWaitPeriod());

        /**
         * Opening stream to XMPP server
         */
        $this->send(Xml::OPEN_TAG);

        $this->authenticate($this->options->getAuthType(), $this->options->getUsername(), $this->options->getPassword());
        $this->setResource($this->options->getResource());

        /**
         * Initial presence stanza sent to server to check roster and send
         * presence notification to each person subscribed to you
         */
        $this->send('<presence/>');
    }

    /**
     * Sending XML stanzas to open socket
     * @param $xml
     * @return bool
     */
    public function send(string $xml)
    {
        try {
            fwrite($this->socket, $xml);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Send message to Jabber user
     * TODO: refactor to use object for type instead of string
     *
     * @param $message
     * @param $to
     * @param $type
     */
    public function sendMessage(string $message, string $to, string $type = "CHAT")
    {
        $this->send($this->message->sendMessage(Xml::quote($message), Xml::quote($to), $type));
    }

    /**
     * Set resource
     * @param $resource
     */
    private function setResource(string $resource)
    {
        if (empty($resource) || trim($resource) == '')
            return;

        $this->send($this->iq->setResource(Xml::quote($resource)));
    }

    /**
     * Get roster for currently authenticated user
     */
    public function getRoster()
    {
        $this->send($this->iq->getRoster());
    }

    // TODO: not working? Not getting a server response

    /**
     * Ask the user to accept a presence subscription event
     * Response should be:
     * - subscribed is user accepted
     * - unsubscribed if user declined
     *
     * @param $to
     * @param string $type
     */
    public function requestPresence($to, $type = "subscribe")
    {
        $this->send($this->presence->requestPresence(Xml::quote($this->options->bareJid()), Xml::quote($to), Xml::quote($type)));
    }

    /**
     * Authenticate user with given XMPP server
     * @param AuthTypeInterface $authType
     * @param $username
     * @param $password
     */
    private function authenticate(AuthTypeInterface $authType, $username, $password)
    {
        $preparedString = Auth::authenticate($authType, $username, $password);
        $this->send($preparedString);
    }

    /**
     * Get response from server if any.
     * @param bool $echoOutput
     * @return string
     */
    public function getResponse($echoOutput = false): string
    {
        $response = '';
        while ($out = fgets($this->socket)) {
            $response .= $out;
        }

        if ($echoOutput && $response)
            echo "\n-------------\n $response \n-------------\n";

        return $response;
    }

    /**
     * Set priority to current resource by default, or optional other resource tied to the
     * current username
     * @param int $priority
     * @param string|null $resource
     */
    public function setPriority(int $priority, string $resource = null)
    {
        if ($resource == null)
            $from = Xml::quote($this->options->fullJid());
        else
            $from = $this->options->getUsername() . "/$resource";

        $this->send($this->presence->setPriority($priority, $from));
    }

    /**
     * Extracting messages from the response
     * @return array
     */
    public function getMessages(): array
    {
        return Xml::parseTag($this->getResponse(), "message");
    }

    /**
     * End the XMPP session and close the socket
     */
    public function disconnect()
    {
        $this->send(Xml::CLOSE_TAG);
        fclose($this->socket);
    }
}