<?php

require_once 'Entity.php';

class LikedPost extends Entity
{
    protected $table = 'postsLiked';

    protected $columns = ['postId', 'userId'];

    protected $hidden = [];

    protected $postId;

    protected $userId;

    function __construct(int $postId, int $userId)
    {
        parent::__construct();
        $this->postId = $postId;
        $this->userId = $userId;
    }

    function getPostId(): string
    {
        return $this->postId;
    }

    function getUserId(): int
    {
        return $this->userId;
    }
}
