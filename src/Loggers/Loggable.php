<?php

namespace Norgul\Xmpp\Loggers;

interface Loggable
{
    /**
     * Standard log message
     * @param $message
     */
    public function log($message);

    /**
     * Shorthand method for logging with prepended "REQUEST" string
     * @param $message
     */
    public function logRequest($message);

    /**
     * Shorthand method for logging with prepended "RESPONSE" string
     * @param $message
     */
    public function logResponse($message);

    /**
     * Shorthand method for logging with prepended "ERROR" string
     * @param $message
     */
    public function error($message);

    /**
     * Returns relative path from given resource
     * @param $resource
     * @return string
     */
    public function getFilePathFromResource($resource): string;
}
