<?php

require_once '/var/www/camagru/src/infrastructure/repository/PostRepository.php';
require_once '/var/www/camagru/src/infrastructure/repository/LikedPostRepository.php';

class PostManager
{
    const USER_ID_NOT_SET = 'user id not set';
    const IMAGE_NOT_SET = 'image not set';
    const IMAGE_URL_NOT_SET = 'image url not set';
    const DESCRIPTION_NOT_SET = 'description not set';

    private $postRepository;

    private $likedPostRepository;

    private $errors;

    function __construct()
    {
        $this->postRepository = new PostRepository();
        $this->likedPostRepository = new LikedPostRepository();
        $this->errors = [];
    }

    function post($userId, $post)
    {
        if (!$this->dataForPostIsValid($userId, $post)) {
            return null;
        }
        return $this->postRepository->add(
            [
                'userId' => $userId,
                'imageUrl' => $post['imageUrl'],
                'description' => $post['description']
            ]
        );
    }

    function dataForPostIsValid($userId, $post)
    {
        if (!$userId) {
            $this->errors['userId'] = self::USER_ID_NOT_SET;
        }
        if (!$post) {
            $this->errors['image'] = self::IMAGE_NOT_SET;
        }
        if (!isset($post['imageUrl'])) {
            $this->errors['imageUrl'] = self::IMAGE_URL_NOT_SET;
        }
        if (!isset($post['description'])) {
            $this->errors['description'] = self::DESCRIPTION_NOT_SET;
        }
        return !count($this->getErrors());
    }

    function dataForLikedPostIsValid($likedPost)
    {
        if (!isset($likedPost['postId']) || !$likedPost['postId']) {
            $this->errors['postId'] = self::POST_ID_NOT_SET;
        }

        if (!isset($likedPost['userId']) || !$likedPost['userId']) {
            $this->errors['userId'] = self::USER_ID_NOT_SET;
        }

        return !count($this->getErrors());
    }

    function getUploadedByUserId($userId)
    {
        return $this->postRepository->getUploadedByUserId($userId);
    }

    function deletePostByUserIdPostId($userId, $postId)
    {
        return $this->postRepository->deletePostByUserIdPostId($userId, $postId);
    }

    function getPosts($count, $offset)
    {
        return $this->postRepository->getPosts($count, $offset);
    }

    function likePost($postId, $userId)
    {
        $this->postRepository->like($postId);

        $this->likedPostRepository->add(
            [
                'postId' => $postId,
                'userId' => $userId
            ]
        );
    }

    function dislikePost($postId, $userId)
    {
        $this->postRepository->dislike($postId);
        $this->likedPostRepository->delete($postId, $userId);
    }

    function postsLikedByUser($userId)
    {
        return $this->likedPostRepository->getByUserId($userId);
    }

    function getErrors(): array
    {
        return $this->errors;
    }
}
