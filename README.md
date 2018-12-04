# PHP library for XMPP

This is low level socket implementation for enabling PHP to 
communicate with XMPP due to lack of such libraries online (at least ones I 
could find). 

Current version is oriented towards simplicity and XMPP understanding under the
hood. Should the need arise, I will expand the library, and by all means feel
free to contribute to the repository. 

## Install and example

After initial `composer install`, the library is ready to go.

You can see usage example in `Example.php` file by changing credentials to 
point to your XMPP server and from project root run `php src/Test.php`.

## Library usage

In order to start using the library you first need to instantiate a new `Connector` 
class. Everything except setting a port number is required. If omitted, port 
will default to `5222` which is XMPP default. 

Usage:
```
$connector = new Connector();

$connector
    ->setHost($host)
    ->setPort($port)
    ->setUsername($username)
    ->setPassword($password);
```

Connector object is required for establishing the connection and every other subsequent
request, so once set it should not be changed. 

Once this is set you can instantiate new client object and pass the connector object in.

```
$client = new XmppClient();
$client->connect($connector);
```

Client `connect()` method ultimately calls `Socket.php` class `connect()` method which
does few things:
1. Connects to the socket which was initialized in `XmppClient` constructor
2. Opens XML stream to exchange with XMPP server
3. Tries to authorize with the server based on provided credentials