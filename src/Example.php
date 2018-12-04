<?php

namespace Norgul\Xmpp;

class Example
{
    public static function go()
    {
        $host = 'host.example.com';
        $port = 5222;
        $username = 'foo';
        $password = 'bar';

        $connector = new Connector();

        $connector
            ->setHost($host)
            ->setPort($port)
            ->setUsername($username)
            ->setPassword($password);

        $client = new XmppClient();
        $client->connect($connector);

        $client->terminateConnection();
    }
}

require __DIR__.'/../vendor/autoload.php';
Example::go();
