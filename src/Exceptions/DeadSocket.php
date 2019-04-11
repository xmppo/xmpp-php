<?php


namespace Norgul\Xmpp\Exceptions;


class DeadSocket extends \Exception
{
    public function __construct() {
        $errorCode = socket_last_error();
        $errorMsg = socket_strerror($errorCode);

        $message = "Couldn't create socket: [$errorCode] $errorMsg";

        parent::__construct($message);
    }
}