<?php

require_once '/var/www/camagru/config/database.php';


class MysqlConnection
{
    static function connect()
    {
        try {
            $connection = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD']);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $connection;
        } catch (PDOException $error) {
            echo 'Connection failed: ' . $error->getMessage();
        }
    }
}
