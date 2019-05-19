<?php

namespace Norgul\Xmpp;

use Exception;
use Norgul\Xmpp\Buffers\Response;
use Norgul\Xmpp\Exceptions\DeadSocket;

class Socket
{
    public $connection;

    protected $responseBuffer;
    protected $options;

    /**
     * Period in seconds during which the socket will be active when doing a socket_read()
     */
    protected $timeout = 1;

    /**
     * Socket constructor.
     * @param Options $options
     * @param Response $responseBuffer
     * @throws DeadSocket
     */
    public function __construct(Options $options, Response $responseBuffer)
    {
        $this->responseBuffer = $responseBuffer;
        $this->connection = stream_socket_client($options->fullSocketAddress());

        if (!$this->isAlive($this->connection)) {
            throw new DeadSocket();
        }

        stream_set_timeout($this->connection, $this->timeout);
        $this->options = $options;
    }

    public function disconnect()
    {
        fclose($this->connection);
    }

    /**
     * Sending XML stanzas to open socket
     * @param $xml
     */
    public function send(string $xml)
    {
        try {
            fwrite($this->connection, $xml);
            $this->options->getLogger()->logRequest(__METHOD__ . '::' . __LINE__ . " $xml");
        } catch (Exception $e) {
            $this->options->getLogger()->error(__METHOD__ . '::' . __LINE__ . " fwrite() failed " . $e->getMessage());
            return;
        }

        $this->receive();
    }

    public function receive()
    {
        $response = '';
        while ($out = fgets($this->connection)) {
            $response .= $out;
        }

        if (!$response) {
            return;
        }

        $this->responseBuffer->write($response);
        $this->options->getLogger()->logResponse(__METHOD__ . '::' . __LINE__ . " $response");
    }

    protected function isAlive($socket)
    {
        return $socket !== false;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function getResponseBuffer(): Response
    {
        return $this->responseBuffer;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }
}
