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

    public static function response($message)
    {
        if(!$message || $message == '' || empty($message))
            return;

        echo "\n****** DATA RECEIVED: " . date("Y/m/d h:i:sa") . " ******\n";

        if (gettype($message) != 'string' && get_resource_type($message) == 'xml') {
            echo print_r($message, true);
        } else {
            echo str_replace("><", ">\n<", $message);
        }

        echo "\n****************************************************\n\n";
    }
}