<?php

require_once '/var/www/camagru/src/infrastructure/MysqlConnection.php';
require_once '/var/www/camagru/src/infrastructure/entity/LikedPost.php';

class LikedPostRepository
{
    private $table = 'postsLiked';

    private $connection;

    function __construct()
    {
        $this->connection = MysqlConnection::connect();
    }

    function add($likedPost)
    {
        $newLikedPost = new LikedPost($likedPost['postId'], $likedPost['userId']);
        $newLikedPost->save();
        return $newLikedPost->toArray();
    }

    function delete($postId, $userId)
    {
        try {
            $query = "DELETE FROM {$this->getTable()} WHERE postId=? AND userId=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$postId, $userId]);
            $this->connection->commit();
            return true;
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return false;
        }
    }

    function getByUserId($userId)
    {
        try {
            $query = "SELECT * FROM {$this->getTable()} WHERE userId=?";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$userId]);
            $this->connection->commit();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
