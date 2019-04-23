<?php

namespace Norgul\Xmpp;

use Norgul\Xmpp\Authentication\AuthTypes\Authenticable;
use Norgul\Xmpp\Authentication\AuthTypes\Plain;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
     * Protocol used for socket connection, defaults to TCP
     */
    protected $protocol = 'tcp';
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
    protected $resource;
    /**
     * PSR-3 logger interface
     * @var $logger LoggerInterface
     */
    protected $logger;
    /**
     * Does server support TLS?
     */
    protected $useTls = false;
    /**
     * @var Authenticable $authType
     */
    protected $authType;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function getHost()
    {
        if (!$this->host) {
            $this->logger->error("No host found, please set the host variable");
            throw new InvalidArgumentException();
        }

        return $this->host;
    }

    public function setHost(string $host): Options
    {
        $this->host = trim($host);
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
        if (!$this->username) {
            $this->logger->error("No username found, please set the username variable");
            throw new InvalidArgumentException();
        }

        return $this->username;
    }

    /**
     * Try to assign a resource if it exists. If bare JID is forwarded, this will default to your username
     *
     * @param string $username
     * @return Options
     */
    public function setUsername(string $username): Options
    {
        $usernameResource = explode('/', $username);

        if (count($usernameResource) > 1) {
            $this->setResource($usernameResource[1]);
            $username = $usernameResource[0];
        }

        $this->username = trim($username);

        return $this;
    }

    public function getPassword()
    {
        if (!$this->password) {
            $this->logger->error("No password found, please set the password variable");
            throw new InvalidArgumentException();
        }

        return $this->password;
    }

    public function setPassword(string $password): Options
    {
        $this->password = $password;
        return $this;
    }

    public function getResource()
    {
        if (!$this->resource) {
            $this->resource = 'norgul_machine_' . time();
        }

        return $this->resource;
    }

    public function setResource(string $resource): Options
    {
        $this->resource = trim($resource);
        return $this;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function setProtocol(string $protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    public function fullSocketAddress()
    {
        $protocol = $this->getProtocol();
        $host = $this->getHost();
        $port = $this->getPort();

        return "$protocol://$host:$port";
    }

    public function fullJid()
    {
        $username = $this->getUsername();
        $resource = $this->getResource();
        $host = $this->getHost();

        return "$username@$host/$resource";
    }

    public function bareJid()
    {
        $username = $this->getUsername();
        $host = $this->getHost();

        return "$username@$host";
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function setUseTls(bool $enable)
    {
        $this->useTls = $enable;
    }

    public function usingTls(): bool
    {
        return $this->useTls;
    }

    public function getAuthType()
    {
        if (!$this->authType) {
            $this->setAuthType(new Plain($this));
        }

        return $this->authType;
    }

    public function setAuthType($authType)
    {
        $this->authType = $authType;
        return $this;
    }
}
