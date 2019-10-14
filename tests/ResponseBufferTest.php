<?php

use Norgul\Xmpp\Buffers\Response;
use PHPUnit\Framework\TestCase;

class ResponseBufferTest extends TestCase
{
    /**
     * @var $buffer Response
     */
    public $buffer;

    protected function setUp()
    {
        $this->buffer = new Response();
    }

    public function testWriteString()
    {
        $this->buffer->write("test");
        $this->assertEquals(["test"], $this->buffer->getCurrentBufferData());
    }

    public function testWriteNumber()
    {
        $this->buffer->write(123);
        $this->assertEquals(['123'], $this->buffer->getCurrentBufferData());
    }

    public function testWriteEmpty()
    {
        $this->buffer->write("");
        $this->assertEquals(null, $this->buffer->getCurrentBufferData());
    }

    public function testWriteNull()
    {
        $this->buffer->write(null);
        $this->assertEquals(null, $this->buffer->getCurrentBufferData());
    }

    public function testRead()
    {
        $this->buffer->write("test");
        $response = $this->buffer->read();
        $this->assertEquals("test", $response);
    }

    public function testReadNullInput()
    {
        $this->buffer->write(null);
        $response = $this->buffer->read();
        $this->assertEquals("", $response);
    }

    public function testFlushWithInput()
    {
        $this->buffer->write("test");
        $this->buffer->read();
        $this->assertEquals(null, $this->buffer->getCurrentBufferData());
    }

    public function testFlushWithoutInput()
    {
        $this->buffer->read();
        $this->assertEquals(null, $this->buffer->getCurrentBufferData());
    }
}
