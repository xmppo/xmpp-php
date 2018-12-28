<?php

namespace Norgul\Xmpp;

use Exception;
use Norgul\Xmpp\Authentication\Auth;
use Norgul\Xmpp\Authentication\AuthTypes\Plain;

/**
 * Class Socket
 * @package Norgul\Xmpp
 */
class XmppClient
{
    protected $socket;
    protected $options;

    /**
     * XmppClient constructor. Initializing a new socket
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function getSocket()
    {
        return $this->socket;
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

        $this->authenticate($this->options->getUsername(), $this->options->getPassword());
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
        $preparedString = str_replace(
            ['{message}', '{to}', '{type}'],
            [Xml::quote($message), Xml::quote($to), Xml::quote($type)],
            Xml::MESSAGE
        );

        $this->send($preparedString);
    }

    /**
     * Set resource
     * @param $resource
     */
    public function setResource(string $resource)
    {
        if (empty($resource) || trim($resource) == '')
            return;

        $preparedString = str_replace('{resource}', Xml::quote($resource), Xml::RESOURCE);
        $this->send($preparedString);
    }

    /**
     * Get roster for currently authenticated user
     */
    public function getRoster()
    {
        $this->send(Xml::ROSTER);
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
        $preparedString = str_replace(
            ['{from}', '{to}', '{type}'],
            [Xml::quote($this->options->fullJid()), Xml::quote($to), Xml::quote($type)],
            Xml::PRESENCE
        );

        $this->send($preparedString);
    }

    /**
     * Authenticate user with given XMPP server
     * @param $username
     * @param $password
     */
    public function authenticate($username, $password)
    {
        $preparedString = Auth::authenticate(new Plain(), $username, $password);
        $this->send($preparedString);
    }

    /**
     * Get response from server if any.
     * @return string
     */
    public function getResponse(): string
    {
        $response = '';
        while ($out = fgets($this->socket)) {
            $response .= $out;
        }

        return $response;
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