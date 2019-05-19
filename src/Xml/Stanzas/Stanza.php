<?php

namespace Norgul\Xmpp\Xml\Stanzas;

use Norgul\Xmpp\Socket;
use Norgul\Xmpp\Xml\Xml;

abstract class Stanza
{
    use Xml;

    protected $socket;

    public function __construct(Socket $socket)
    {
        $this->socket = $socket;
    }

    protected function uniqueId(): string
    {
        return uniqid();
    }

    protected function readResponseFile()
    {
        $logger = $this->socket->getOptions()->getLogger();
        $responseFilePath = $logger->getFilePathFromResource($logger->log);
        $responseFile = fopen($responseFilePath, 'r');

        return fread($responseFile, filesize($responseFilePath));
    }
}
