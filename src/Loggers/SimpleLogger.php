<?php

namespace Norgul\Xmpp\Loggers;

class SimpleLogger extends Logger
{
    protected function getLogPath()
    {
        return self::LOG_FOLDER . '/';
    }
}
