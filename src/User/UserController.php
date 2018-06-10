<?php

namespace HybridLogin\User;


use HybridLogin\Controller\ContextControllerInterface;
use HybridLogin\Controller\Controller;
use HybridLogin\Error\ErrorMessagesInterface;

/**
 * Class UserController
 * @package HybridLogin\User
 */
class UserController implements ContextControllerInterface
{
    /**
     * @var Controller $parentController
     */
    private $parentController;
    /**
     * @var UserService $userService
     */
    protected $userService;


    /**
     * UserController constructor.
     * @param Controller $controller
     * @param UserService $userService
     */
    public function __construct(Controller $controller, UserService $userService)
    {
        $this->parentController = $controller;
        $this->userService = $userService;
    }


    /**
     * @inheritdoc
     */
    public function getParentController(): Controller
    {
        return $this->parentController;
    }


    /**
     * Checks if an email is registered
     */
    public function isRegistered(): void
    {
        $email = $_REQUEST['email'] ?? null;
        if (null === $email) {
            $this->parentController->getResponse()->addError(ErrorMessagesInterface::INVALID_EMAIL);
            return;
        }

        $user = $this->userService->findOneByEmail($email);
        if (null !== $user) {
            $this->parentController->getResponse()->setNode('isRegistered', true);
            $this->parentController->getResponse()->setNode('userId', $user->getUUID());
        } else {
            $this->parentController->getResponse()->setNode('isRegistered', false);
        }
    }


    /**
     * Sign in
     */
    public function signIn(): void
    {
        $email = $_REQUEST['email'] ?? null;
        if (null === $email) {
            $this->parentController->getResponse()->addError(ErrorMessagesInterface::INVALID_EMAIL);
        }
        $password = $_REQUEST['password'] ?? null;
        if (null === $password) {
            $this->parentController->getResponse()->addError(ErrorMessagesInterface::INVALID_PASSWORD);
        }
        if ($this->parentController->getResponse()->hasError()) {
            return;
        }

        $user = $this->userService->findOneByEmail($email);
        if (null !== $user && $this->userService->login($user, $password)) {
            $this->parentController->getResponse()->setNode('isRegistered', true);
            $this->parentController->getResponse()->setNode('isLoggedIn', true);
            $this->parentController->getResponse()->setNode('userUUID', (string) $user->getUUID());
        } else {
            $this->parentController->getResponse()->setNode('isLoggedIn', false);
            $this->parentController->getResponse()->addErrors($this->userService->getErrorHandler()->getErrors());
        }
    }


    /**
     * Sign up
     */
    public function signUp(): void
    {
        $user = $this->userService->createFromArray($_REQUEST);
        if (true === $this->userService->save($user) && true === $this->userService->login($user, $_REQUEST['password'])) {
            $this->parentController->getResponse()->setNode('isRegistered', true);
            $this->parentController->getResponse()->setNode('isLoggedIn', true);
            $this->parentController->getResponse()->setNode('userUUID', (string) $user->getUUID());
        } else {
            $this->parentController->getResponse()->setNode('isLoggedIn', false);
            $this->parentController->getResponse()->addErrors($this->userService->getErrorHandler()->getErrors());
        }
    }


    /**
     * Logout
     */
    public function logout(): void
    {
        $this->userService->logout();
    }
}
