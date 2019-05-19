<?php


namespace Norgul\Xmpp\Exceptions;

use Exception;

class StreamError extends Exception
{
    public function __construct($streamErrorType)
    {
        parent::__construct("Unrecoverable stream error ({$streamErrorType}), trying to reconnect...");
    }
}
