<?php

namespace Norgul\Xmpp\Buffers;

interface Buffer
{
    public function write($data);

    public function read();

    public function flush();
}
