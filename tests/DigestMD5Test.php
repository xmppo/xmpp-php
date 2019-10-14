<?php

use Norgul\Xmpp\AuthTypes\DigestMD5;
use Norgul\Xmpp\Options;
use PHPUnit\Framework\TestCase;

class DigestMD5Test extends TestCase
{
    /**
     * @var $digestAuth DigestMD5
     */
    public $digestAuth;
    /**
     * @var $optionsStub Options
     */
    public $optionsStub;

    protected function setUp()
    {
        $this->optionsStub = $this->createMock(Options::class);
        $this->optionsStub->method('getUsername')->willReturn('Foo');
        $this->optionsStub->method('getPassword')->willReturn('Bar');

        $this->digestAuth = new DigestMD5($this->optionsStub);
    }

    public function testIfCredentialsAreEncodedRight()
    {
        $this->assertEquals("80e25ae4140c338afbe621c1b3d7a9ec9480f731", $this->digestAuth->encodedCredentials());
    }
}
