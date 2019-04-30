<?php

namespace Norgul\Xmpp;

use Psr\Log\LoggerInterface;

class ResponseLogger implements LoggerInterface
{
    protected $log;
    protected $response;
    protected $request;

    public function __construct()
    {
        $this->log = fopen('xmpp.log', 'w');
        $this->response = fopen('response.xml', 'w');
        $this->request = fopen('request.xml', 'w');
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
        fwrite($this->log, $this->interpolate($message, $context) . "\n");
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = array())
    {
        fwrite($this->log, $this->interpolate($message, $context) . "\n");
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = array())
    {
        fwrite($this->log, $this->interpolate($message, $context) . "\n");
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = array())
    {
        fwrite($this->log, $this->interpolate($message, $context) . "\n");
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = array())
    {
        fwrite($this->log, $this->interpolate($message, $context) . "\n");
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = array())
    {
        fwrite($this->log, $this->interpolate($message, $context) . "\n");
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = array())
    {
        $file = $this->fileSwitcher($message);
        $message = $this->clean($message);
        fwrite($file, $this->interpolate($message, $context) . "\n");
    }

    /**“
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = array())
    {
        fwrite($this->log, $this->interpolate($message, $context) . "\n");
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        fwrite($this->log, "$level {$this->interpolate($message, $context)}\n");
    }

    /**
     * Interpolates context values into the message placeholders.
     * @param $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        $interpolated = strtr($message, $replace);

        return $interpolated;
    }

    protected function matchXml(string $interpolated, $file, $type)
    {
        preg_match_all("#($type)::.*?::\d+(.*)#", $interpolated, $match);

        if (count($match) < 1 || !is_array($match[1]) || empty($match[1])) {
            return false;
        }

        fwrite($file, $match[1][0]);

        return true;
    }

    protected function fileSwitcher($log)
    {
        preg_match_all("#(.*?)::.*?::\d+(.*)#", $log, $match);

        if (count($match) < 1 || !is_array($match[1]) || empty($match[1])) {
            return $this->log;
        }

        switch ($match[1][0]) {
            case "REQUEST":
                return $this->request;
                break;
            case "RESPONSE":
                return $this->response;
                break;
            default:
                return $this->log;
        }
    }

    protected function clean($log)
    {
        preg_match_all("#.*?::.*?::\d+(.*)#", $log, $match);

        if (count($match) < 1 || !is_array($match[1]) || empty($match[1])) {
            return '';
        }

        return $match[1][0];
    }
}
