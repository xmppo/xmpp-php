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

## Custom usage