<?php

require_once '/var/www/camagru/src/infrastructure/repository/UserRepository.php';
require_once '/var/www/camagru/src/domain/email/EmailManager.php';
require_once '/var/www/camagru/src/domain/auth/UserValidator.php';

class UserManager
{
    private $userRepository;

    private $userValidator;

    private $emailManager;

    private $errors;

    function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userValidator = new UserValidator();
        $this->emailManager = new EmailManager();
        $this->errors = [];
    }

    function register($user)
    {
        if (!$this->userValidator->validUserForRegistratration($user)) {
            $this->errors = $this->userValidator->getErrors();
            return false;
        }

        $user = $this->userRepository->add($user);

        $this->emailManager->sendVerificationEmail($user->getEmail(), $user->getVkey(), $user->getUsername());

        return $user->toArray();
    }

    function verify($vkey): bool
    {
        return $this->userRepository->verify($vkey);
    }

    function login($user): bool
    {
        if (!$this->userValidator->validUserForLogin($user)) {
            $this->errors = $this->userValidator->getErrors();
            return false;
        }

        if (!isset($_SESSION)) {
            session_start();
        }
        $user = $this->userRepository->getByUsername($user['username']);
        $_SESSION['id'] = $user['id'];
        return true;
    }

    function logout($request)
    {
        $request->clearSession();
        if (!isset($_SESSION)) {
            session_start();
        }
        session_unset();
        session_destroy();
        $_SESSION = array();
        setcookie (session_name(), "", time() - 3600);
    }

    function update($user): bool
    {
        $success = true;

        if (!$this->userValidator->hasId($user)) {
            $success = false;
        }
        if (isset($user['email']) && (!$this->userValidator->validEmail($user['email']) || !$this->userValidator->isUniqueEmail($user['email']))) {
            $success = false;
        }
        if (isset($user['username']) && (!$this->userValidator->validUsername($user['username']) || !$this->userValidator->isUniqueUsername($user['username']))) {
            $success = false;
        }
        if (isset($user['password']) && (!$this->userValidator->validPassword($user['password']))) {
            $success = false;
        }

        if (!$success) {
            $this->errors = $this->userValidator->getErrors();
            return false;
        }

        if (isset($user['email'])) {
            $this->userRepository->updateEmail($user['id'], $user['email']);
        }

        if (isset($user['username'])) {
            $this->userRepository->updateUsername($user['id'], $user['username']);
        }

        if (isset($user['notifyAboutComments'])) {
            $this->userRepository->updateNotifyAboutComments($user['id'], $user['notifyAboutComments']);
        }

        if (isset($user['password'])) {
            $this->userRepository->updatePasswordById($user['id'], $user['password']);
        }

        return true;
    }

    function getErrors(): array
    {
        return $this->errors;
    }
}
