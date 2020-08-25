<?php

class UserValidator
{
    const ID_NOT_SET = 'Id not set';
    const USERNAME_NOT_SET = 'Username not set';
    const EMAIL_NOT_SET = 'Email not set';
    const PASSWORD_NOT_SET = 'Password not set';

    const USERNAME_EMPTY = 'Username empty';
    const EMAIL_EMPTY = 'Email empty';
    const PASSWORD_EMPTY = 'Password empty';

    const INVALID_USERNAME = 'Invalid username';
    const INVALID_EMAIL = 'Invalid email';
    const INVALID_PASSWORD = 'Password must contain<br>
  1 lowercase letter<br>
  1 uppercase letter<br>
  1 number<br>
  1 special character (@#-_$%^&+=§!?)<br>
  8 to 12 characters';

    const EXISTING_USERNAME = 'Existing username';
    const EXISTING_EMAIL = 'Existing email';

    const INCORRECT_USERNAME = 'Incorrect username';
    const INCORRECT_EMAIL = 'Incorrect email';
    const INCORRECT_PASSWORD = 'Incorrect password';

    const NOT_VERIFIED_USER = 'Please, confirm your account via email we sent you';
    const NO_CREDENTIALS_PROVIDED = 'Please provide username or email to identify you';

    private $userRepository;

    private $errors = [];

    function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    function validUsername($username): bool
    {
        $isValid = true;

        if (empty($username)) {
            $this->errors['username'] = self::USERNAME_EMPTY;
            $isValid = false;
        }

        if (!preg_match('/^[a-zA-Z0-9]*$/', $username)) {
            $this->errors['username'] = self::INVALID_USERNAME;
            $isValid = false;
        }

        return $isValid;
    }

    function validEmail($email): bool
    {
        $isValid = true;

        if (empty($email)) {
            $this->errors['email'] = self::EMAIL_EMPTY;
            $isValid = false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = self::INVALID_EMAIL;
            $isValid = false;
        }

        return $isValid;
    }

    function validPassword($password): bool
    {
        $isValid = true;

        if (empty($password)) {
            $this->errors['password'] = self::PASSWORD_EMPTY;
            $isValid = false;
        }

        if (!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,12}$/', $password)) {
            $this->errors['password'] = self::INVALID_PASSWORD;
            $isValid = false;
        }

        return $isValid;
    }

    function hasId($user): bool
    {
        $hasId = true;

        if (!isset($user['id'])) {
            $this->errors['id'] = self::ID_NOT_SET;
            $hasId = false;
        }

        return $hasId;
    }

    function hasValidRegistratrationForm($user): bool
    {
        $isValid = true;

        if (!isset($user['username'])) {
            $this->errors['username'] = self::USERNAME_NOT_SET;
            $isValid = false;
        }

        if (!isset($user['email'])) {
            $this->errors['email'] = self::EMAIL_NOT_SET;
            $isValid = false;
        }

        if (!isset($user['password'])) {
            $this->errors['password'] = self::PASSWORD_NOT_SET;
            $isValid = false;
        }

        if (!$isValid) {
            return false;
        }

        return ($this->validUsername($user['username']) && $this->validEmail($user['email']) && $this->validPassword($user['password']));
    }

    function hasValidLoginForm($user): bool
    {
        $isValid = true;

        if (!isset($user['username'])) {
            $this->errors['username'] = self::USERNAME_NOT_SET;
            $isValid = false;
        }

        if (!isset($user['password'])) {
            $this->errors['password'] = self::PASSWORD_NOT_SET;
            $isValid = false;
        }

        if (!$isValid) {
            return false;
        }

        return ($this->validUsername($user['username']) && $this->validPassword($user['password']));
    }

    function hasValidPasswordResetRequestForm($user): bool
    {
        $isValid = true;

        if (!isset($user['email'])) {
            $this->errors['email'] = self::EMAIL_NOT_SET;
            $isValid = false;
        }

        if (!$isValid) {
            return false;
        }

        return $this->validEmail($user['email']);
    }

    function isUniqueEmail($email): bool
    {
        $isUniqueEmail = true;

        if (empty($email)) {
            $this->errors['email'] = self::EMAIL_EMPTY;
            $isUniqueEmail = false;
        }

        if (!$isUniqueEmail) {
            return false;
        }

        if ($this->userRepository->getByEmail($email)) {
            $this->errors['email'] = self::EXISTING_EMAIL;
            $isUniqueEmail = false;
        }

        return $isUniqueEmail;
    }

    function isUniqueUsername($username): bool
    {
        $isUniqueUsername = true;

        if (empty($username)) {
            $this->errors['username'] = self::USERNAME_EMPTY;
            $isUniqueUsername = false;
        }

        if (!$isUniqueUsername) {
            return false;
        }

        if ($this->userRepository->getByUsername($username)) {
            $this->errors['username'] = self::EXISTING_USERNAME;
            $isUniqueUsername = false;
        }

        return $isUniqueUsername;
    }

    function isRegistered($user): bool
    {
        $isRegistered = true;

        if (!isset($user['username']) && !isset($user['email'])) {
            $this->errors['credentials'] = self::NO_CREDENTIALS_PROVIDED;
            $isRegistered = false;
        }

        if (!$isRegistered) {
            return false;
        }

        if (isset($user['username']) && !$this->userRepository->getByUsername($user['username'])) {
            $this->errors['username'] = self::INCORRECT_USERNAME;
            $isRegistered = false;
        } else if (isset($user['email']) && !$this->userRepository->getByEmail($user['email'])) {
            $this->errors['email'] = self::INCORRECT_EMAIL;
            $isRegistered = false;
        }

        return $isRegistered;
    }

    function isVerifiedByUsername($user): bool
    {
        $isVerified = true;

        if (!isset($user['username'])) {
            $this->errors['username'] = self::NOT_VERIFIED_USER;
            $isVerified = false;
        }

        if (isset($user['username'])) {
            $userInDb = $this->userRepository->getByUsername($user['username']);
            if (!isset($userInDb['verified']) || !$userInDb['verified']) {
                $this->errors['username'] = self::NOT_VERIFIED_USER;
                $isVerified = false;
            }
        }

        return $isVerified;
    }

    function isVerifiedByEmail($user): bool
    {
        $isVerified = true;

        if (!isset($user['email'])) {
            $this->errors['email'] = self::NOT_VERIFIED_USER;
            $isVerified = false;
        }

        if (isset($user['email'])) {
            $userInDb = $this->userRepository->getByEmail($user['email']);
            if (!isset($userInDb['verified']) || !$userInDb['verified']) {
                $this->errors['email'] = self::NOT_VERIFIED_USER;
                $isVerified = false;
            }
        }

        return $isVerified;
    }

    function hasCorrectPassword($user): bool
    {
        $hasCorrectPassword = true;

        if (!isset($user['username'])) {
            $this->errors['username'] = self::USERNAME_NOT_SET;
            $hasCorrectPassword = false;
        }

        if (!isset($user['password'])) {
            $this->errors['password'] = self::PASSWORD_NOT_SET;
            $hasCorrectPassword = false;
        }

        if (!$hasCorrectPassword) {
            return false;
        }

        $userInDb = $this->userRepository->getByUsername($user['username']);

        if (!password_verify($user['password'], $userInDb['password'])) {
            $this->errors['password'] = self::INCORRECT_PASSWORD;
            $hasCorrectPassword = false;
        }

        return $hasCorrectPassword;
    }

    function validUserForRegistratration($user): bool
    {
        if (!$this->hasValidRegistratrationForm($user)) {
            return false;
        }
        if (!$this->isUniqueUsername($user['username']) || !$this->isUniqueEmail($user['email'])) {
            return false;
        }
        return true;
    }

    function validUserForLogin($user): bool
    {
        if (!$this->hasValidLoginForm($user)) {
            return false;
        }
        if (!$this->isRegistered($user)) {
            return false;
        }
        if (!$this->isVerifiedByUsername($user)) {
            return false;
        }
        if (!$this->hasCorrectPassword($user)) {
            return false;
        }
        return true;
    }

    function validUserForPasswordResetRequest($user): bool
    {
        if (!$this->hasValidPasswordResetRequestForm($user)) {
            return false;
        }
        if (!$this->isRegistered($user)) {
            return false;
        }
        if (!$this->isVerifiedByEmail($user)) {
            return false;
        }
        return true;
    }

    function getErrors(): array
    {
        return $this->errors;
    }
}
