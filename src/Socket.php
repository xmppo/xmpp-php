<?php

namespace Norgul\Xmpp;

use Exception;

class Socket
{
    public  $connection;

    /**
     * Period in seconds during which the socket will be active when doing a socket_read()
     */
    protected $timeout = 1;

    public function __construct(string $fullSocketAddress)
    {
        $this->connection = stream_socket_client($fullSocketAddress);
        $this->checkIfAlive($this->connection);
        stream_set_timeout($this->connection, $this->timeout);
    }

    protected function checkIfAlive($socket)
    {
        if ($socket !== false)
            return;

        $errorCode = socket_last_error();
        $errorMsg = socket_strerror($errorCode);

        die("Couldn't create socket: [$errorCode] $errorMsg");
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