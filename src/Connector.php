<?php

namespace Norgul\Xmpp;

class Connector
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

    public function getHost()
    {
        if(!$this->host)
            echo "No host found, please set the host variable ";
        return $this->host;
    }

    public function setHost($host): Connector
    {
        $this->host = $host;
        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port): Connector
    {
        $this->port = $port;
        return $this;
    }

    public function getUsername()
    {
        if(!$this->username)
            echo "No username found, please set the username variable ";
        return $this->username;
    }

    public function setUsername($username): Connector
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword()
    {
        if(!$this->host)
            echo "No password found, please set the password variable ";
        return $this->password;
    }

    public function setPassword($password): Connector
    {
        $this->password = $password;
        return $this;
    }


}