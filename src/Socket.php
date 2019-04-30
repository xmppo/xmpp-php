<?php

namespace Norgul\Xmpp;

use Exception;
use Norgul\Xmpp\Exceptions\DeadSocket;

class Socket
{
    public $connection;

    /**
     * Period in seconds during which the socket will be active when doing a socket_read()
     */
    protected $timeout = 1;
    protected $options;

    /**
     * Socket constructor.
     * @param Options $options
     * @throws DeadSocket
     */
    public function __construct(Options $options)
    {
        $this->connection = stream_socket_client($options->fullSocketAddress());

        if (!$this->isAlive($this->connection)) {
            throw new DeadSocket();
        }

        stream_set_timeout($this->connection, $this->timeout);
        $this->options = $options;
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
            $this->options->getLogger()->info("REQUEST::" . __METHOD__ . '::' . __LINE__ . $xml);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function receive()
    {
        $response = '';
        while ($out = fgets($this->connection)) {
            $response .= $out;
        }

        $this->options->getLogger()->info("RESPONSE::" . __METHOD__ . '::' . __LINE__ . $response);
        return $response;
    }
}
