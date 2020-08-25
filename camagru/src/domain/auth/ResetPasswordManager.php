<?php

require_once '/var/www/camagru/src/infrastructure/repository/ResetPasswordRepository.php';
require_once '/var/www/camagru/src/infrastructure/repository/UserRepository.php';
require_once '/var/www/camagru/src/domain/email/EmailManager.php';
require_once '/var/www/camagru/src/domain/auth/UserValidator.php';

class ResetPasswordManager
{
    const NON_EXISTING_REQUEST = 'This reset password request does not exist';

    private $resetPasswordRepository;

    private $userRepository;

    private $userValidator;

    private $errors;

    private $emailManager;

    function __construct()
    {
        $this->resetPasswordRepository = new ResetPasswordRepository();
        $this->userRepository = new UserRepository();
        $this->userValidator = new UserValidator();
        $this->emailManager = new EmailManager();
        $this->errors = [];
    }

    function addResetPasswordRequest($user)
    {
        if (!$this->userValidator->validUserForPasswordResetRequest($user)) {
            $this->errors = $this->userValidator->getErrors();
            return false;
        }

        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);
        $expires = date("U") + 1800;

        $resetPasswordRequest = $this->resetPasswordRepository->add($user['email'], $selector, $token, $expires);
        $this->emailManager->sendResetPasswordEmail($user['email'], $selector, $token);

        return $resetPasswordRequest;
    }

    function verify($selector, $token): bool
    {
        if (!ctype_xdigit($selector) || !ctype_xdigit($token)) {
            return false;
        }

        if ($this->resetPasswordRepository->requestIsExpired($selector)) {
            return false;
        }

        return $this->tokenIsValid($selector, $token);
    }

    function tokenIsValid($selector, $token): bool
    {
        try {
            $resetPasswordRequest = $this->resetPasswordRepository->getBySelector($selector);
            if (!$resetPasswordRequest) {
                return false;
            }
            if (password_verify(hex2bin($token), $resetPasswordRequest['token'])) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $error) {
            $this->connection->rollBack();
            return false;
        }
    }

    function resetPassword($data): bool
    {
        $resetPasswordRequest = $this->resetPasswordRepository->getBySelector($data['selector']);
        if (!$resetPasswordRequest) {
            $this->errors['request'] = self::NON_EXISTING_REQUEST;
            return false;
        }
        $email = $resetPasswordRequest['email'];

        if (!$this->userValidator->validPassword($data['newPassword'])) {
            $this->errors = $this->userValidator->getErrors();
            return false;
        }

        $this->userRepository->updatePassword($email, $data['newPassword']);
        $this->resetPasswordRepository->deleteExistingRequest($email);
        return true;
    }

    function getErrors(): array
    {
        return $this->errors;
    }
}
