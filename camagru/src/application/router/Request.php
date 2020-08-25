<?php

require_once 'IRequest.php';

if (!isset($_SESSION)) {
    session_start();
}

class Request implements IRequest
{
    private $parameters;

    private $variables;

    private $body;

    private $session;

    function __construct()
    {
        $this->parameters = [];
        $this->variables = [];
        $this->body = [];
        $this->session = [];
        $this->bootstrapSelf();
    }

    private function bootstrapSelf()
    {
        foreach ($_SERVER as $key => $value) {
            $this->{$this->toCamelCase($key)} = $value;
        }

        if ($this->requestMethod == "GET") {
            foreach ($_GET as $key => $value) {
                $this->variables[$key] = $value;
            }
        }

        if ($this->requestMethod == "POST") {
            $rest_json = file_get_contents("php://input");
            $_POST = json_decode($rest_json, true);
            $body = array();
            if (!$_POST) {
                header("{$this->serverProtocol} 400 Bad Request");
                die;
            }
            foreach ($_POST as $key => $value) {
                $body[$key] = $value;
            }
            $this->body = $body;
        }

        if (isset($_SESSION['id'])) {
            foreach ($_SESSION as $key => $value) {
                $this->session[$key] = $value;
            }
        }
    }

    private function toCamelCase($string)
    {
        $result = strtolower($string);

        preg_match_all('/_[a-z]/', $result, $matches);

        foreach ($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }

        return $result;
    }

    public function getBody()
    {
        if ($this->requestMethod === "GET") {
            return;
        }

        if ($this->requestMethod == "POST") {
            return $this->body;
        }
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function clearParameters()
    {
        $this->parameters = [];
    }

    public function getSession()
    {
        return $this->session;
    }

    public function clearSession()
    {
        $this->session = [];
    }
}
