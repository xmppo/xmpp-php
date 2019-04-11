<?php

namespace Norgul\Xmpp;

use Exception;
use Norgul\Xmpp\Exceptions\DeadSocket;

class Socket
{
    public  $connection;

    /**
     * Period in seconds during which the socket will be active when doing a socket_read()
     */
    protected $timeout = 1;

    /**
     * Socket constructor.
     * @param string $fullSocketAddress
     * @throws DeadSocket
     */
    public function __construct(string $fullSocketAddress)
    {
        $this->connection = stream_socket_client($fullSocketAddress);

        if(!$this->isAlive($this->connection))
            throw new DeadSocket();

        stream_set_timeout($this->connection, $this->timeout);
    }

    protected function isAlive($socket)
    {
        return $socket !== false;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Sending XML stanzas to open socket
     * @param $xml
     * @return bool
     */
    public function send(string $xml)
    {
        try {
            fwrite($this->connection, $xml);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}