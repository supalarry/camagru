<?php

require_once 'Entity.php';

class ResetPasswordRequest extends Entity
{
    protected $table = 'passwordReset';

    protected $columns = ['email', 'selector', 'token', 'expires'];

    protected $hidden = [];

    protected $email;

    protected $selector;

    protected $token;

    protected $expires;

    function __construct(string $email, string $selector, string $token, string $expires)
    {
        parent::__construct();
        $this->email = $email;
        $this->selector = $selector;
        $this->token = $token;
        $this->expires = $expires;
    }

    function getEmail(): string
    {
        return $this->email;
    }

    function setEmail(string $email)
    {
        $this->email = $email;
    }

    function getSelector(): string
    {
        return $this->selector;
    }

    function setSelector(string $selector)
    {
        $this->selector = $selector;
    }

    function getToken(): string
    {
        return $this->token;
    }

    function setToken(string $token)
    {
        $this->token = $token;
    }

    function getExpires(): string
    {
        return $this->expires;
    }
}
