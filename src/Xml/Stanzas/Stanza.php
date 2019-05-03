<?php

namespace Norgul\Xmpp\Xml\Stanzas;

use Norgul\Xmpp\Options;
use Norgul\Xmpp\Socket;
use Norgul\Xmpp\Xml\Xml;

abstract class Stanza
{
    use Xml;

    protected $socket;
    protected $options;

    const RESPONSE_FILE_PATH = 'response.xml';
    protected $responseFile;

    public function __construct(Socket $socket, Options $options)
    {
        $this->socket = $socket;
        $this->options = $options;
        $this->responseFile = fopen(self::RESPONSE_FILE_PATH, 'r');
    }

    protected function uniqueId(): string
    {
        return uniqid();
    }

    protected function readResponseFile()
    {
        return fread($this->responseFile, filesize(self::RESPONSE_FILE_PATH));
    }
}
