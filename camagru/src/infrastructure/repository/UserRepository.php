<?php

require_once '/var/www/camagru/src/infrastructure/entity/User.php';
require_once '/var/www/camagru/src/infrastructure/MysqlConnection.php';

class UserRepository
{
    private $table = 'users';

    private $connection;

    function __construct()
    {
        $this->connection = MysqlConnection::connect();
    }

    function add($user)
    {
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        $vkey = md5(time() . $user['username']);
        $user = new User($user['username'], $user['email'], $hashedPassword, $vkey);
        $user->save();
        return $user;
    }

    function verify($vkey): bool
    {
        try {
            $query = "SELECT * FROM {$this->getTable()} WHERE verified = 0 AND vkey=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$vkey]);
            $this->connection->commit();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $query = "UPDATE users SET verified = 1 WHERE vkey=? LIMIT 1";
                $stmt = $this->getConnection()->prepare($query);
                $this->connection->beginTransaction();
                $stmt->execute([$vkey]);
                $this->connection->commit();
                return true;
            } else {
                return false;
            }
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return false;
        }
    }

    function getProfileInformation($id)
    {
        $user = $this->getById($id);
        unset($user['password']);
        unset($user['vkey']);
        unset($user['verified']);
        return $user;
    }

    function getById($id)
    {
        try {
            $query = "SELECT * FROM {$this->getTable()} WHERE id=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$id]);
            $this->connection->commit();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return null;
        }
    }

    function getByUsername($username)
    {
        try {
            $query = "SELECT * FROM {$this->getTable()} WHERE username=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$username]);
            $this->connection->commit();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return null;
        }
    }

    function getByEmail($email)
    {
        try {
            $query = "SELECT * FROM {$this->getTable()} WHERE email=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$email]);
            $this->connection->commit();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return null;
        }
    }

    function updatePassword($email, $newPassword): bool
    {
        try {
            if (!$this->getByEmail($email)) {
                return false;
            }
            $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password=? WHERE email=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$newPasswordHashed, $email]);
            $this->connection->commit();
            return true;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return false;
        }
    }

    function updatePasswordById($id, $newPassword): bool
    {
        try {
            if (!$this->getById($id)) {
                return false;
            }
            $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password=? WHERE id=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$newPasswordHashed, $id]);
            $this->connection->commit();
            return true;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return false;
        }
    }

    function updateEmail($id, $newEmail): bool
    {
        try {
            $query = "UPDATE users SET email=? WHERE id=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$newEmail, $id]);
            $this->connection->commit();
            return true;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return false;
        }
    }

    function updateUsername($id, $newUsername): bool
    {
        try {
            $query = "UPDATE users SET username=? WHERE id=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$newUsername, $id]);
            $this->connection->commit();
            return true;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return false;
        }
    }

    function updateNotifyAboutComments($id, $newNotifyAboutComments): bool
    {
        try {
            $query = "UPDATE users SET notifyAboutComments=? WHERE id=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$newNotifyAboutComments, $id]);
            $this->connection->commit();
            return true;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return false;
        }
    }

    function commentNotificationsEnabled($userId): bool
    {
        $user = $this->getById($userId);
        if (isset($user['notifyAboutComments'])) {
            return $user['notifyAboutComments'];
        }
        return false;
    }

    function userCount(): int
    {
        try {
            $query = "SELECT COUNT(*) FROM {$this->getTable()}";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([]);
            $this->connection->commit();
            return $stmt->fetchColumn();
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return 0;
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
