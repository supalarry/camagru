<?php

require_once '/var/www/camagru/src/infrastructure/repository/CommentRepository.php';
require_once '/var/www/camagru/src/infrastructure/repository/UserRepository.php';
require_once '/var/www/camagru/src/infrastructure/repository/PostRepository.php';
require_once '/var/www/camagru/src/domain/email/EmailManager.php';

class CommentManager
{
    const POST_ID_NOT_SET = 'post id not set';
    const COMMENTATOR_ID_INCORRECT = 'commentator id incorrect';
    const CONTENT_NOT_SET = 'content not set';

    private $commentRepository;

    private $userRepository;

    private $postRepository;

    private $emailManager;

    private $errors;

    function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->commentRepository = new CommentRepository();
        $this->postRepository = new PostRepository();
        $this->emailManager = new EmailManager();
        $this->errors = [];
    }

    function commentPost($comment)
    {
        if (!$this->dataForCommentIsValid($comment)) {
            return null;
        }
        $commentator = $this->userRepository->getById($comment['commentatorId']);
        if (!$commentator) {
            $this->errors['commentator'] = self::COMMENTATOR_ID_INCORRECT;
            return null;
        }
        if ($this->postCreatorWantsToReceiveNotification($comment['postId'])) {
            $this->sendNewCommentNotificationEmail($comment['postId'], $commentator['username'], $comment['content']);
        }
        return $this->commentRepository->add(
            [
                'postId' => $comment['postId'],
                'commentatorId' => $comment['commentatorId'],
                'commentatorUsername' => $commentator['username'],
                'content' => $comment['content']
            ]
        );
    }

    function dataForCommentIsValid($comment)
    {
        if (!isset($comment['postId'])) {
            $this->errors['postId'] = self::POST_ID_NOT_SET;
        }
        if (!isset($comment['commentatorId'])) {
            $this->errors['commentatorId'] = self::COMMENTATOR_ID_NOT_SET;
        }
        if (!isset($comment['content'])) {
            $this->errors['imageUrl'] = self::CONTENT_NOT_SET;
        }
        return !count($this->getErrors());
    }

    function getByPostId($postId)
    {
        return $this->commentRepository->getByPostId($postId);
    }

    function sendNewCommentNotificationEmail($postId, $commentator, $comment)
    {
        $postOwnerId = $this->postRepository->getUserIdOfPost($postId);
        $postOwner = $this->userRepository->getById($postOwnerId);
        $this->emailManager->sendNewCommentNotificationEmail(
            $postOwner['email'],
            $commentator,
            $comment
        );
    }

    function postCreatorWantsToReceiveNotification($postId): bool
    {
        $postCreatorId = $this->postRepository->getUserIdOfPost($postId);
        if ($postCreatorId) {
            return $this->userRepository->commentNotificationsEnabled($postCreatorId);
        }
        return false;
    }

    function getErrors(): array
    {
        return $this->errors;
    }
}
