<?php

namespace Norgul\Xmpp;

use Norgul\Xmpp\Authorization\AuthInterface;

class Auth
{

    protected function authorize($username, $password, AuthInterface $authType)
    {
        $authString = $authType::encodedCredentials($username, $password);




        $this->send(XML::AUTH);
    }



}