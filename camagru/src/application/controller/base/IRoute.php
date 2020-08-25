<?php

interface IRoute
{
    public function getType();

    public function getPath();

    public function getMethod();

    public function getIsProtected();

    public function getCantAccessWhileLoggedIn();
}
