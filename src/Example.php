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

        $client->setResource('/resource');

        $client->sendMessage('Hello world', 'test@jabber.com');

        $client->getRawResponse();

        $client->disconnect();


//        do {
//            try {
//                echo "*** Data ***\n\n";
//                while ($out = socket_read($client->getSocket(), 2048)) {
//                    echo str_replace("><", ">\n<",$out) . "\n\n";
//                }
//                echo "\n\n************\n";
//            } catch (Exception $e) {
//                echo "Error\n";
//                echo $e;
//            }
//
//            $line = readline("\nEnter XML: ");
//
//            if ($line && $line != 'exit')
//                socket_write($client->getSocket(), $line, strlen($line));
//
//        } while ($line != 'exit');

    }
}

require __DIR__ . '/../vendor/autoload.php';
Example::go();
