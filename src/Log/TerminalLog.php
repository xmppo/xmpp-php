<?php

namespace Norgul\Xmpp\Log;

use PHPUnit\Runner\Exception;

/**
 * Helper class for echoing to terminal
 *
 * Class TerminalLog
 * @package Norgul\Xmpp\Log
 */
class TerminalLog
{
    public static function info($message)
    {
        echo __METHOD__ . ":" . __LINE__ . " -> $message\n";
    }

    public static function error($message = '')
    {
        throw new Exception($message);
    }
}