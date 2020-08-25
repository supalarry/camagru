<?php

require_once 'Entity.php';

class User extends Entity
{
    protected $table = 'users';

    protected $columns = ['username', 'email', 'password', 'vkey'];

    protected $hidden = ['password', 'vkey'];

    protected $username;

    protected $email;

    protected $password;

    protected $vkey;

    function __construct(string $username, string $email, string $password, string $vkey)
    {
        parent::__construct();
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->vkey = $vkey;
    }

    function getUsername(): string
    {
        return $this->username;
    }

    function setUsername(string $username)
    {
        $this->username = $username;
    }

    function getEmail(): string
    {
        return $this->email;
    }

    function setEmail(string $email)
    {
        $this->email = $email;
    }

    function getPassword(): string
    {
        return $this->password;
    }

    function setPassword(string $password)
    {
        $this->password = $password;
    }

    function getVkey(): string
    {
        return $this->vkey;
    }
}
