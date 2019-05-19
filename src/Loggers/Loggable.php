<?php

namespace Norgul\Xmpp\Loggers;

interface Loggable
{
    public function log($message);

    public function logRequest($message);

    public function logResponse($message);

    public function error($message);
}
