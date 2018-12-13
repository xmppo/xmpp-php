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

        $options = new Options();

        $options
            ->setHost($host)
            ->setPort($port)
            ->setUsername($username)
            ->setPassword($password);

        $client = new XmppClient($options);
        $client->connect();

        // can be omitted if username is of type JID/resource
        $client->setResource('/resource');

        $client->getRoster();

        $client->sendMessage('Hello world', 'test@jabber.com');

        $client->getRawResponse();

        do{
            $client->getParsedResponse();
        }while(true);

//        // Uncomment if you want to manually enter raw XML and see a server response
//        do {
//            $client->getRawResponse();
//
//            $line = readline("\nEnter XML: ");
//
//            if ($line && $line != 'exit')
//                socket_write($client->getSocket(), $line, strlen($line));
//
//        } while ($line != 'exit');

        $client->disconnect();
    }
}

require __DIR__ . '/vendor/autoload.php';
Example::go();
