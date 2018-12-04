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

        echo "*** Data ***\n\n";
        while ($out = socket_read($this->socket, 2048)) {
            echo str_replace("><", ">\n<",$out) . "\n\n";
        }
        echo "\n\n************\n";
    }

    /**
     * Sending XML stanzas to open socket
     * @param $xml
     */
    public function send($xml)
    {
        socket_write($this->socket, $xml, strlen($xml));
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



}