<?php

namespace Norgul\Xmpp\Loggers;

class FullLogger extends Logger
{
    protected function getLogPath()
    {
        $prefix = uniqid(date("ymdTHms") . "_");
        return self::LOG_FOLDER . '/' . $prefix . '_';
    }
}
