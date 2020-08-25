<?php

require_once 'Entity.php';

class Post extends Entity
{
    protected $table = 'posts';

    protected $columns = ['userId', 'imageUrl', 'description'];

    protected $hidden = [];

    protected $userId;

    protected $imageUrl;

    protected $description;

    function __construct(int $userId, string $imageUrl, string $description)
    {
        parent::__construct();
        $this->userId = $userId;
        $this->imageUrl = $imageUrl;
        $this->description = $description;
    }

    function getUserId(): int
    {
        return $this->userId;
    }

    function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    function getPostDescription(): string
    {
        return $this->description;
    }
}
