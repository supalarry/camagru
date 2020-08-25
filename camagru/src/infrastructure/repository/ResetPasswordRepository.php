<?php

require_once '/var/www/camagru/src/infrastructure/entity/ResetPasswordRequest.php';
require_once '/var/www/camagru/src/infrastructure/MysqlConnection.php';

class ResetPasswordRepository
{
    private $table = 'passwordReset';

    private $connection;

    function __construct()
    {
        $this->connection = MysqlConnection::connect();
    }

    function add($email, $selector, $token, $expires)
    {
        $this->deleteExistingRequest($email);
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $resetPasswordRequest = new ResetPasswordRequest($email, $selector, $hashedToken, $expires);
        $resetPasswordRequest->save();
        return $resetPasswordRequest->toArray();
    }

    function deleteExistingRequest($email)
    {
        try {
            $query = "DELETE FROM {$this->getTable()} WHERE email=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$email]);
            $this->connection->commit();
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return null;
        }
    }

    function requestIsExpired($selector): bool
    {
        try {
            $currentDate = date("U");
            $query = "SELECT * FROM {$this->getTable()} WHERE selector=? AND expires < {$currentDate} LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$selector]);
            $this->connection->commit();
            $resetPasswordRequest = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resetPasswordRequest) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return false;
        }
    }

    function getBySelector($selector)
    {
        try {
            $query = "SELECT * FROM {$this->getTable()} WHERE selector=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$selector]);
            $this->connection->commit();
            $resetPasswordRequest = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resetPasswordRequest) {
                return $resetPasswordRequest;
            } else {
                return null;
            }
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return null;
        }
    }

    function getTable()
    {
        return $this->table;
    }

    function getConnection()
    {
        return $this->connection;
    }
}
