<?php

namespace Norgul\Xmpp;

use Exception;
use Norgul\Xmpp\Authentication\Auth;
use Norgul\Xmpp\Authentication\AuthTypes\Plain;
use Norgul\Xmpp\Log\TerminalLog;

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
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
        $this->options = $options;
        TerminalLog::info('Socket created');
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
        $result = socket_connect($this->socket, $this->options->getHost(), $this->options->getPort());
        $result ? TerminalLog::info("Socket connected") : TerminalLog::error("Socket connection failed. $result " . socket_strerror(socket_last_error($this->socket)));

        /**
         * Opening stream to XMPP server
         */
        $this->send(Xml::OPEN_TAG);

        /**
         * Try to assign a resource if it exists. If bare JID is forwarded, this will default to your username
         */
        $username = $this->splitUsernameResource($this->options->getUsername());

        $this->authenticate($username, $this->options->getPassword());
        $this->setResource($this->options->getResource());
    }

    /**
     * Sending XML stanzas to open socket
     * @param $xml
     */
    public function send(string $xml)
    {
        socket_write($this->socket, $xml, strlen($xml));
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
        if(empty($resource) || $resource == '')
            return;

        $preparedString = str_replace('{resource}', Xml::quote($resource), Xml::RESOURCE);
        $this->send($preparedString);
    }

    /**
     * Get roster for current authenticated user
     */
    public function getRoster()
    {
        $this->send(Xml::ROSTER);
    }

    /**
     * Ask the user to accept a presence subscription event
     *
     * @param $to
     * @param string $type
     */
    public function subscribe($to, $type = "subscribe")
    {
        $preparedString = str_replace(
            ['{to}', '{type}'],
            [Xml::quote($to), Xml::quote($type)],
            Xml::PRESENCE
        );

        $this->send($preparedString);
    }

    public function authenticate($username, $password)
    {
        $preparedString = Auth::authenticate(new Plain(), $username, $password);
        $this->send($preparedString);
    }

    /**
     * Get response from server if any
     */
    public function getRawResponse()
    {
        // Wait max 3 seconds before terminating the socket
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO,
            ["sec" => $this->options->getSocketWaitPeriod(), "usec" => 0]);
        try {
            while ($out = socket_read($this->socket, 2048)) {
                echo "*** Data ***\n\n";
                echo str_replace("><", ">\n<", $out) . "\n\n";
                echo "\n\n************\n";
            }
        } catch (Exception $e) {
            echo "Error\n";
            echo $e;
        }

        // Reset waiting period to infinite
        //socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 0));
    }

    /**
     * End the XMPP session and close the socket
     */
    public function disconnect()
    {
        $this->send(Xml::CLOSE_TAG);
        socket_close($this->socket);
        TerminalLog::info('Socket closed');
    }

    /**
     * Try to extract resource from JID. If not present, this will default to
     *
     * @param string $username
     * @return string
     */
    protected function splitUsernameResource(string $username): string
    {
        $usernameResource = explode('/', $username);

        if (count($usernameResource) > 1) {
            $this->options->setResource($usernameResource[1]);
            return $usernameResource[0];
        }
        return $username;
    }


}