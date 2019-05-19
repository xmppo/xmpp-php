<?php

namespace Norgul\Xmpp\Buffers;

class Response
{
    protected $response;

    public function write($data)
    {
        if (!$data) {
            return;
        }

        $this->response[] = $data;
    }

    public function read()
    {
        $response = implode('', $this->response);
        $this->flush();
        return $response;
    }

    public function flush()
    {
        $this->response = [];
    }
}
