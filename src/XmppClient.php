<?php namespace Norgul\Xmpp;

/**
 * Main class for communicating with XMPP server
 *
 * Class XmppClient
 * @package Norgul\Xmpp
 */
class XmppClient
{
    protected $socket;

    /**
     * XmppClient constructor. Initializing a new socket
     */
    public function __construct()
    {
        $this->socket = new Socket();
    }

    /**
     * Connecting to provided server using the given credentials.
     *
     * @param Connector $connector
     */
    public function connect(Connector $connector)
    {
        $this->socket->connect($connector);
    }

    /**
     * End the session by closing the XML document with end tag and close the socket
     */
    public function terminateConnection()
    {
        $this->socket->closeStream();
        $this->socket->terminateConnection();
    }


}