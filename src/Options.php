<?php

namespace Norgul\Xmpp;

use Norgul\Xmpp\Log\TerminalLog;

class Options
{
    /**
     * Hostname of XMPP server
     */
    protected $host;
    /**
     * XMPP server port. Usually 5222
     */
    protected $port = 5222;
    /**
     * Username to authenticate on XMPP server
     */
    protected $username;
    /**
     * Password to authenticate on XMPP server
     */
    protected $password;
    /**
     * XMPP resource
     */
    protected $resource = '';
    /**
     * Period in seconds during which the socket will be active when doing a socket_read()
     */
    protected $socketWaitPeriod = 1;

    public function getHost()
    {
        if(!$this->host)
            TerminalLog::error("No host found, please set the host variable");
        return $this->host;
    }

    public function setHost(string $host): Options
    {
        $this->host = $host;
        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort(int $port): Options
    {
        $this->port = $port;
        return $this;
    }

    public function getUsername()
    {
        if(!$this->username)
            TerminalLog::error("No username found, please set the username variable");
        return $this->username;
    }

    public function setUsername(string $username): Options
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword()
    {
        if(!$this->password)
            TerminalLog::error("No password found, please set the password variable");
        return $this->password;
    }

    public function setPassword(string $password): Options
    {
        $this->password = $password;
        return $this;
    }

    public function getSocketWaitPeriod(): int
    {
        return $this->socketWaitPeriod;
    }

    public function setSocketWaitPeriod(int $socketWaitPeriod): Options
    {
        $this->$socketWaitPeriod = $socketWaitPeriod;
        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setResource($resource): Options
    {
        $this->resource = $resource;
        return $this;
    }

}