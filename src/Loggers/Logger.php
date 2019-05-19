<?php

namespace Norgul\Xmpp\Loggers;

class Logger implements Loggable
{
    public $log;

    const LOG_FOLDER = "logs";
    const LOG_FILE = "xmpp.log";

    public function __construct()
    {
        if (!file_exists(self::LOG_FOLDER)) {
            mkdir(self::LOG_FOLDER, 0777, true);
        }

        $this->log = fopen(self::LOG_FOLDER . '/' . self::LOG_FILE, 'a');
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
        $prefix = date("Y.m.d H:m:s") . " " . session_id() . ($type ? " {$type}::" : " ");
        $this->writeToFile($this->log, $prefix . "$message\n");
    }

    public function parseBySession($sessionId)
    {
        $logFile = fopen(self::LOG_FOLDER . '/' . self::LOG_FILE, 'r');
        $responseFilePath = $this->getFilePathFromResource($logFile);
        $log = fread($logFile, filesize($responseFilePath));

        preg_match_all("#\d{4}\.\d{2}.\d{2} \d{2}:\d{2}:\d{2} (.*?) (REQUEST|RESPONSE).*?(<.*>)#", $log, $matches);

        echo print_r($matches, true);

        if (count($matches) < 2 || !is_array($matches[1]) || empty($matches[1])) {
            return;
        }

//        $response = fopen(self::LOG_FOLDER . '/' . $sessionId . '_response.log', 'w');
    }

    protected function matchXml(string $interpolated, $file, $type)
    {
        preg_match_all("#($type)::.*?::\d+(.*)#", $interpolated, $match);

        if (count($match) < 1 || !is_array($match[1]) || empty($match[1])) {
            return false;
        }

        $this->writeToFile($file, $match[1][0]);

        return true;
    }

    protected function clean($log)
    {
        preg_match_all("#.*?::.*?::\d+(.*)#", $log, $match);

        if (count($match) < 1 || !is_array($match[1]) || empty($match[1])) {
            return '';
        }

        return $match[1][0];
    }

    public function getFilePathFromResource($resource)
    {
        $metaData = stream_get_meta_data($resource);
        return $metaData["uri"];
    }

    protected function writeToFile($file, $message)
    {
        try {
            fwrite($file, $message);
        } catch (\Exception $e) {
            // silent fail
        }
    }
}
