<?php

require_once '/var/www/camagru/src/infrastructure/MysqlConnection.php';
require_once '/var/www/camagru/src/infrastructure/entity/Comment.php';
require_once '/var/www/camagru/src/infrastructure/repository/PostRepository.php';

class CommentRepository
{
    private $table = 'comments';

    private $connection;

    private $postRepository;

    function __construct()
    {
        $this->connection = MysqlConnection::connect();
        $this->postRepository = new PostRepository();
    }

    function add($comment)
    {
        $newComment = new Comment(
            $comment['postId'],
            $comment['commentatorId'],
            $comment['content']
        );
        $newComment->save();
        $this->postRepository->comment($comment['postId']);
        return $newComment->toArray();
    }

    function getByPostId($postId)
    {
        try {
            $query = "SELECT * FROM {$this->getTable()} WHERE postId=? ORDER BY id DESC";
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute([$postId]);
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
