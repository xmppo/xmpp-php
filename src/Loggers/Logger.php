<?php

namespace Norgul\Xmpp\Loggers;

use Exception;

class Logger implements Loggable
{
    public $log;

    const LOG_FOLDER = "logs";
    const LOG_FILE = "xmpp.log";

    public function __construct()
    {
        $this->createLogFile();
        $this->log = fopen(self::LOG_FOLDER . '/' . self::LOG_FILE, 'a');
    }

    protected function createLogFile(): void
    {
        if (!file_exists(self::LOG_FOLDER)) {
            mkdir(self::LOG_FOLDER, 0777, true);
        }
    }

    public function log($message)
    {
        $this->writeToLog($message);
    }

    public function logRequest($message)
    {
        $this->writeToLog($message, "REQUEST");
    }

    public function logResponse($message)
    {
        $this->writeToLog($message, "RESPONSE");
    }

    public function error($message)
    {
        $this->writeToLog($message, "ERROR");
    }

    protected function writeToLog($message, $type = ''): void
    {
        $prefix = date("Y.m.d H:i:s") . " " . session_id() . ($type ? " {$type}::" : " ");
        $this->writeToFile($this->log, $prefix . "$message\n");
    }

    protected function writeToFile($file, $message)
    {
        try {
            fwrite($file, $message);
        } catch (Exception $e) {
            // silent fail
        }
    }

    public function getFilePathFromResource($resource): string
    {
        $metaData = stream_get_meta_data($resource);
        return $metaData["uri"];
    }
}
