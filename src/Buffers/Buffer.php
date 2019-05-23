<?php

namespace Norgul\Xmpp\Buffers;

interface Buffer
{
    /**
     * Write to buffer (add to array of values)
     * @param $data
     */
    public function write($data);

    /**
     * Read from buffer and delete the data
     */
    public function read();
}
