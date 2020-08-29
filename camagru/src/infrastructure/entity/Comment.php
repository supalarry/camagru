<?php

require_once 'Entity.php';

class Comment extends Entity
{
    protected $table = 'comments';

    protected $columns = ['postId', 'commentatorId', 'content'];

    protected $hidden = [];

    protected $postId;

    protected $commentatorId;

    protected $content;

    function __construct(int $postId, int $commentatorId, string $content)
    {
        parent::__construct();
        $this->postId = $postId;
        $this->commentatorId = $commentatorId;
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

    function getContent(): string
    {
        return $this->content;
    }
}
