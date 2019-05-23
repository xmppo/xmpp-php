<?php


namespace Norgul\Xmpp\Exceptions;

use Exception;

class DeadSocket extends Exception
{
    public function __construct()
    {
        $errorCode = socket_last_error();
        $errorMsg = socket_strerror($errorCode);

        parent::__construct("Couldn't create socket: [$errorCode] $errorMsg");
    }
}
