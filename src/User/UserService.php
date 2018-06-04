<?php

namespace HybridLogin\User;

use HybridLogin\Error\ErrorHandlerInterface;
use HybridLogin\Error\ErrorMessagesInterface;
use HybridLogin\Model\AbstractEntity;
use HybridLogin\Model\AbstractService;

/**
 * Class UserService
 * @package HybridLogin\User
 */
class UserService extends AbstractService
{
    /**
     * UserService constructor.
     * @param ErrorHandlerInterface $errorHandler
     * @param UserRepository $repository
     */
    public function __construct(ErrorHandlerInterface $errorHandler, UserRepository $repository)
    {
        parent::__construct($errorHandler, $repository);
    }


    /**
     * @return User
     */
    public function create(): AbstractEntity
    {
        return new User($this->getErrorHandler());
    }


    /**
     * @param array $userData
     * @param bool $fromRepository
     * @return User
     */
    public function createFromArray(array $userData, bool $fromRepository = false): AbstractEntity
    {
        $user = $this->create();
        $user->setUUID($userData['uuid'] ?? null);
        $user->setEmail($userData['email'] ?? null);
        $user->setPassword($userData['password'] ?? null, $fromRepository);
        $user->setActive($userData['active'] ?? false); // 'false' must be the default value of User->active

        return $user;
    }


    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user): bool
    {
        if (!$user->validate()) {
            // var_dump($user->getErrors());
            return false;
        }
        try {
            return $this->repository->save($user);
        } catch (\Exception $e) {
            $this->errorHandler->addError($e->getMessage());
        }
        return false;
    }


    /**
     * @param int $id
     * @return User|null
     */
    public function findOneById(int $id): ?AbstractEntity
    {
        $userData = $this->repository->findOneById($id);
        if (!empty($userData)) {
            return $this->createFromArray($userData, true);
        }

        return null;
    }


    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email): ?AbstractEntity
    {
        $userData = $this->repository->findOneByAttributes(['email' => $email]);
        if (!empty($userData)) {
            return $this->createFromArray($userData, true);
        }

        return null;
    }


    /**
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function login(User $user, string $password): bool
    {
        if ($user->getEncryptedPassword() === UserPassword::encryptPassword($password)) {

            // TODO: implement stateless login with tokens (!?)
            $_SESSION['loggedUserUUID'] = $user->getUUID();
            $_SESSION['loggedUserEmail'] = $user->getEmail();

            return true;
        }

        $this->logout();
        $this->errorHandler->addError(ErrorMessagesInterface::INVALID_PASSWORD);

        // TODO: implement some control over exploitation

        return false;
    }


    /**
     * Logout
     */
    public function logout(): void
    {
        $_SESSION['loggedUserUUID'] = null;
        $_SESSION['loggedUserEmail'] = null;
    }


}
