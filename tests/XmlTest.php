<?php

use Norgul\Xmpp\Xml\Xml;
use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    use Xml;
    public $host = 'www.test.com';

    public function testOpeningStream()
    {
        $expected = "<?xml version='1.0' encoding='UTF-8'?><stream:stream to='www.test.com' xmlns:stream='http://etherx.jabber.org/streams' xmlns='jabber:client' version='1.0'>";
        $this->assertEquals($expected, $this->openXmlStream($this->host));
    }
}
