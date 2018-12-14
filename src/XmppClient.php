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
        $this->options = $options;

//        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO,
//            ["sec" => $this->options->getSocketWaitPeriod(), "usec" => 0]);


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
//        $result = socket_connect($this->socket, $this->options->getHost(), $this->options->getPort());
        $this->socket = stream_socket_client($this->options->getFullSocketAddress());

        // Wait max 3 seconds by default before terminating the socket. Can be changed with options
        stream_set_timeout($this->socket, $this->options->getSocketWaitPeriod());


        // TODO: implement error handling and proper response
        $this->socket ? TerminalLog::info("Socket connected") : TerminalLog::error("Socket connection failed");

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

        /**
         * Initial presence stanza sent to server to check roster and send
         * presence notification to each person subscribed to you
         */
        $this->presence();
    }

    /**
     * Sending XML stanzas to open socket
     * @param $xml
     */
    public function send(string $xml)
    {
        fwrite($this->socket, $xml);
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
        if (empty($resource) || $resource == '')
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

    // TODO: not working? Not getting a server response

    /**
     * Ask the user to accept a presence subscription event
     * Response should be:
     * - subscribed is user accepted
     * - unsubscribed if user declined
     *
     * @param $from
     * @param $to
     * @param string $type
     */
    public function requestPresence($from, $to, $type = "subscribe")
    {
        $preparedString = str_replace(
            ['{from}', '{to}', '{type}'],
            [Xml::quote($from), Xml::quote($to), Xml::quote($type)],
            Xml::PRESENCE
        );

        $this->send($preparedString);
    }

    /**
     * Closed presence stanza, used in initial communication with server
     */
    private function presence()
    {
        $this->send('<presence/>');
    }

    public function authenticate($username, $password)
    {
        $preparedString = Auth::authenticate(new Plain(), $username, $password);
        $this->send($preparedString);
    }

    /**
     * Get raw response from server if any
     */
    public function getRawResponse()
    {
        while ($out = fgets($this->socket)) {
            echo "*** Data ***\n\n";
            echo str_replace("><", ">\n<", $out) . "\n\n";
            echo "\n\n************\n";
        }
    }

    // TODO: find alternative for XML parsing
//    /**
//     * Get parsed response from server if any. Since XMPP session is a continuous
//     * XML, first response from server can't be parsed as XML since it contains
//     * opening tag <stream:stream> without it being closed.
//     */
    public function getParsedResponse()
    {
        $this->getRawResponse();
//        while ($out = socket_read($this->socket, 2048)) {
//            echo "*** Data ***\n\n";
//            $xml = simplexml_load_string($out);
//
//            if ($xml) {
//                echo print_r($xml);
//            } else {
//                echo str_replace("><", ">\n<", $out) . "\n\n";
//            }
//
//            echo "\n\n************\n";
//        }
//
//        while ($out = fgets($this->socket)) {
//            echo "*** Data ***\n\n";
//            echo str_replace("><", ">\n<", $out) . "\n\n";
//            echo "\n\n************\n";
//        }
    }

    /**
     * End the XMPP session and close the socket
     */
    public function disconnect()
    {
        $this->send(Xml::CLOSE_TAG);
//        socket_close($this->socket);
        fclose($this->socket);
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