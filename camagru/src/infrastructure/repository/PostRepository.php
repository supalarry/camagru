<?php

require_once '/var/www/camagru/src/infrastructure/MysqlConnection.php';
require_once '/var/www/camagru/src/infrastructure/entity/Post.php';

class PostRepository
{
    private $table = 'posts';

    private $connection;

    function __construct()
    {
        $this->connection = MysqlConnection::connect();
    }

    function add($post)
    {
        $newPost = new Post($post['userId'], $post['imageUrl'], $post['description']);
        $newPost->save();
        return $newPost->toArray();
    }

    function getUploadedByUserId($userId)
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

    function deletePostByUserIdPostId($userId, $postId)
    {
        try {
            $query = "DELETE FROM {$this->getTable()} WHERE id=? AND userId=? LIMIT 1";
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

    function getPosts($count, $offset)
    {
        try {
            $query = "SELECT * FROM {$this->getTable()} ORDER BY id DESC LIMIT ? OFFSET ?";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$count, $offset]);
            $this->connection->commit();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return null;
        }
    }

    function like($postId)
    {
        try {
            $query = "UPDATE posts SET likes = likes + 1 WHERE id=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$postId]);
            $this->connection->commit();
        } catch (PDOException $error) {
            $this->connection->rollBack();
        }
    }

    function dislike($postId)
    {
        try {
            $query = "UPDATE posts SET likes = likes - 1 WHERE id=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$postId]);
            $this->connection->commit();
        } catch (PDOException $error) {
            $this->connection->rollBack();
        }
    }

    function comment($postId)
    {
        try {
            $query = "UPDATE posts SET comments = comments + 1 WHERE id=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$postId]);
            $this->connection->commit();
        } catch (PDOException $error) {
            $this->connection->rollBack();
        }
    }

    function getUserIdOfPost($postId)
    {
        try {
            $query = "SELECT * FROM {$this->getTable()} WHERE id=? LIMIT 1";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$postId]);
            $this->connection->commit();
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($post) {
                return $post['userId'];
            }
            return 0;
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
