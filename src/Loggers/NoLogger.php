<?php

namespace Norgul\Xmpp\Loggers;

class NoLogger extends Logger
{
    protected function getLogPath()
    {
        return '';
    }
}
