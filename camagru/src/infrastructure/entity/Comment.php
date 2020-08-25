<?php

require_once 'Entity.php';

class Comment extends Entity
{
    protected $table = 'comments';

    protected $columns = ['postId', 'commentatorId', 'commentatorUsername', 'content'];

    protected $hidden = [];

    protected $postId;

    protected $commentatorId;

    protected $commentatorUsername;

    protected $content;

    function __construct(int $postId, int $commentatorId, string $commentatorUsername, string $content)
    {
        parent::__construct();
        $this->postId = $postId;
        $this->commentatorId = $commentatorId;
        $this->commentatorUsername = $commentatorUsername;
        $this->content = $content;
    }

    function getPostId(): int
    {
        return $this->postId;
    }

    function getCommentatorId(): int
    {
        return $this->commentatorId;
    }

    function getCommentatorUsername(): string
    {
        return $this->commentatorUsername;
    }

    function getContent(): string
    {
        return $this->content;
    }
}
