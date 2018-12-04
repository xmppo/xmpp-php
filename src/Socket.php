<?php

namespace Norgul\Xmpp;

use Norgul\Xmpp\Authorization\Plain;

/**
 * Class Socket
 * @package Norgul\Xmpp
 */
class Socket
{
    /**
     * Global socket instance
     */
    protected $socket;

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
        echo "Socket created\n";
    }

    /**
     * Open socket to a given host on a given port
     *
     * @param Connector $connector
     */
    public function connect(Connector $connector)
    {
        $result = socket_connect($this->socket, $connector->getHost(), $connector->getPort());
        echo $result ? "Socket connected\n" : "Socket connection failed. $result " . socket_strerror(socket_last_error($this->socket)) . "\n";

        $this->openStream();
        $this->authorize($connector->getUsername(), $connector->getPassword());
    }

    /**
     * Sending XML stanzas to open socket
     * @param $xml
     */
    public function send($xml)
    {
        socket_write($this->socket, $xml, strlen($xml));
    }

    public function sendMessage($message, $to, $type)
    {
        $preparedString = Xml::quote(str_replace(
            ['{message}', '{to}', '{type}'],
            [$message, $to, $type],
            Xml::MESSAGE
        ));

        $this->send($preparedString);
    }

    /**
     * Opening stream to XMPP server
     */
    public function openStream()
    {
        $this->send(Xml::OPEN_TAG);
    }

    /**
     * Ending the session by sending the closing tag.
     * This does not close the socket connection however, it should be closed with socket_close()
     */
    public function closeStream()
    {
        $this->send(Xml::CLOSE_TAG);
    }

    public function authorize($username, $password)
    {
        $preparedString = Auth::authorize(new Plain(), $username, $password);
        $this->send($preparedString);
    }

    public function terminateConnection()
    {
        socket_close($this->socket);
    }

    /**
     * Get response from server if any
     */
    public function getServerResponse()
    {
        // Wait max 3 seconds before terminating the socket
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 5, "usec" => 0));

        echo "*** Data ***\n\n";
        while ($out = socket_read($this->socket, 2048)) {
            echo str_replace("><", ">\n<",$out) . "\n\n";
        }
        echo "\n\n************\n";

        // Reset waiting period to infinite
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 0));
    }
}