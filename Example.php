<?php

namespace Norgul\Xmpp;

class Example
{
    protected static $host     = 'host.example.com';
    protected static $port     = 5222;
    protected static $username = 'foo';
    protected static $password = 'bar';

    public function __construct()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    public static function go()
    {
        $options = new Options();

        $options
            ->setHost(self::$host)
            ->setPort(self::$port)
            ->setUsername(self::$username)
            ->setPassword(self::$password);

        $client = new XmppClient($options);
        $client->connect();

        $client->getRoster();

        $client->sendMessage('Hello world', 'test@jabber.com');

        $client->getResponse();

        do {
            $client->getResponse();
        } while (true);

        // Uncomment if you want to manually enter raw XML and see a server response
//        (new self)->sendRawXML($client);

        $client->disconnect();
    }

    public function sendRawXML(XmppClient $client)
    {
        do {
            $client->getResponse();

            $line = readline("\nEnter XML: ");

            if ($line && $line != 'exit')
                $client->send($line);

        } while ($line != 'exit');
    }
}

require __DIR__ . '/vendor/autoload.php';
Example::go();
