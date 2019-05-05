<?php

namespace Norgul\Xmpp\Loggers;

class ResponseLogger extends Logger
{
    protected function getLogPath()
    {
        if (!file_exists(self::LOG_FOLDER)) {
            mkdir(self::LOG_FOLDER, 0777, true);
        }

        $prefix = uniqid(date("ymdTHms") . "_");
        return self::LOG_FOLDER . '/' . $prefix . '_';
    }
}
