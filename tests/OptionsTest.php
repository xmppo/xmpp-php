<?php

use Norgul\Xmpp\Options;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;

class OptionsTest extends TestCase
{
    protected $host;
    protected $port;
    protected $username;
    protected $password;

    /**
     * @var $options Options
     */
    public $options;

    protected function setUp()
    {
        $this->host = 'www.host.com';
        $this->username = 'foo';
        $this->password = 'bar';
        $this->port = 5222;
        $this->options = new Options();

        $this->options
            ->setHost($this->host)
            ->setPort($this->port)
            ->setUsername($this->username)
            ->setPassword($this->password);
    }

    public function testIfNoHostThrowsError()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->options->setHost('');
        $this->options->getHost();
    }

    public function testIfNoUsernameThrowsError()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->options->setUsername('');
        $this->options->getUsername();
    }

    public function testIfNoPasswordThrowsError()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->options->setPassword('');
        $this->options->getPassword();
    }

    public function testIfHostGetsTrimmed()
    {
        $this->options->setHost('   host');
        $this->assertEquals('host', $this->options->getHost());

        $this->options->setHost('host    ');
        $this->assertEquals('host', $this->options->getHost());

        $this->options->setHost('   host    ');
        $this->assertEquals('host', $this->options->getHost());
    }

    public function testIfUsernameSplitResource()
    {
        $this->options->setUsername('user/resource');
        $this->assertEquals('user', $this->options->getUsername());
        $this->assertEquals('resource', $this->options->getResource());

        $this->options->setUsername('user');
        $this->assertEquals('user', $this->options->getUsername());

        $this->options->setUsername('user/resource/resource2/resource3');
        $this->assertEquals('user', $this->options->getUsername());
        $this->assertEquals('resource', $this->options->getResource());
    }

    public function testResourcePrecedence()
    {
        $this->options->setUsername('user/resource');
        $this->options->setResource('resource2');
        $this->assertEquals('user', $this->options->getUsername());
        $this->assertEquals('resource2', $this->options->getResource());

        $this->options->setResource('resource2');
        $this->options->setUsername('user/resource');
        $this->assertEquals('user', $this->options->getUsername());
        $this->assertEquals('resource', $this->options->getResource());
    }

    public function testIfResourceGetsTrimmed()
    {
        $this->options->setResource('   resource');
        $this->assertEquals('resource', $this->options->getResource());

        $this->options->setResource('resource    ');
        $this->assertEquals('resource', $this->options->getResource());

        $this->options->setResource('   resource    ');
        $this->assertEquals('resource', $this->options->getResource());
    }

    public function testFullSocketAddress()
    {
        $this->assertEquals("tcp://www.host.com:5222", $this->options->fullSocketAddress());
    }

    public function testFullJid()
    {
        $this->options->setResource('resource');
        $this->assertEquals("foo@www.host.com/resource", $this->options->fullJid());
    }

    public function testBareJid()
    {
        $this->assertEquals("foo@www.host.com", $this->options->bareJid());
    }
}
