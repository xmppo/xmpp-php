<?php

namespace Norgul\Xmpp\Buffers;

class Response implements Buffer
{
    protected $response;

    public function write($data)
    {
        if ($data) {
            $this->response[] = $data;
        }
    }

    public function read()
    {
        $implodedResponse = implode('', $this->response);
        $this->flush();
        return $implodedResponse;
    }

    protected function flush()
    {
        $this->response = [];
    }
}
