<?php

namespace HybridLogin\User;

use HybridLogin\Model\AbstractEntity;
use HybridLogin\Error\ErrorMessagesInterface;

/**
 * Class User
 * @package HybridLogin\User
 *
 * @property int $uuid
 * @property string $email
 * @property string $password
 * @property bool $active
 * @property string $passwordEncrypted
 */
class User extends AbstractEntity
{
    private $email;
    private $password;
    private $passwordEncrypted;
    private $active = false;


    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }


    /**
     * @param mixed $email
     */
    public function setEmail(?string $email): void
    {
        if (!$this->validateEmail($email)) {
            $this->email = null;
            return;
        }
        $this->email = $email;
    }


    /**
     * @param string $email
     * @return bool
     */
    private function validateEmail(?string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }


    /**
     * @param mixed $password
     * @param bool $fromRepository
     */
    public function setPassword(?string $password, bool $fromRepository = false): void
    {
        if (true === $fromRepository) {
            $this->passwordEncrypted = $password;
            return;
        }
        if (false === $this->validatePassword($password, !$fromRepository)) {
            $this->passwordEncrypted = $this->password = null;
            return;
        }
        $this->passwordEncrypted = UserPassword::encryptPassword($password);
    }


    /**
     * @return null|string
     */
    public function getEncryptedPassword(): ?string
    {
        return $this->passwordEncrypted;
    }


    /**
     * @param string $password
     * @param bool $addError
     * @return bool
     */
    private function validatePassword(?string $password, $addError = false): bool
    {
        if (null === $password) {
            return false;
        }
        foreach (UserPassword::getRestrictions() as $rule) {
            if (1 !== preg_match($rule['regularExpression'], $password)) {
                if ($addError) {
                    $this->errorHandler->addError($rule['message']);
                    $valid = false; // If adding errors, just return after the last
                } else {
                    return false; // If not adding errors, one failure is enough to return
                }
            }
        }
        return $valid ?? true;
    }


    /**
     * @return bool
     */
    public function isPasswordSet(): bool
    {
        return $this->password !== null || $this->passwordEncrypted !== null;
    }


    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }


    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }


    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        return [
            'uuid' => $this->uuid,
            'email' => $this->email,
            // 'password' => $this->password,
            'password' => $this->passwordEncrypted,
            'active' => $this->active
        ];
    }


    /**
     * @inheritdoc
     */
    public function validate(): bool
    {
        $valid = true;
        if ($this->passwordEncrypted === null && $this->isNew()) {
            $this->errorHandler->addError(ErrorMessagesInterface::INVALID_PASSWORD);
            $valid = false;
        }
        if (false === $this->validateEmail($this->email)) {
            $this->errorHandler->addError(ErrorMessagesInterface::INVALID_EMAIL);
            $valid = false;
        }
        return $valid;
    }


    /**
     * @inheritdoc
     */
    public function isNew(): bool
    {
        return $this->uuid ? false : true;
    }
}
