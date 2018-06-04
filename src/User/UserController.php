<?php

namespace HybridLogin\User;


use HybridLogin\Controller\AbstractController;
use HybridLogin\Controller\Response;
use HybridLogin\Error\ErrorMessagesInterface;

class UserController extends AbstractController
{
    /**
     * @var UserService $userService
     */
    protected $userService;


    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * @return string
     */
    public function isRegistered(): string
    {
        $response = new Response();

        $email = $_REQUEST['email'] ?? null;
        if (null === $email) {
            $response->addError(ErrorMessagesInterface::INVALID_EMAIL);
            return $this->formatResponse($response);
        }

        $user = $this->userService->findOneByEmail($email);
        if (null !== $user) {
            $response->addData('isRegistered', true);
            $response->addData('userId', $user->getUUID());
        } else {
            $response->addData('isRegistered', false);
        }
        return $this->formatResponse($response);
    }


    /**
     * @return string
     */
    public function signIn(): string
    {
        $response = new Response();

        $email = $_REQUEST['email'] ?? null;
        if (null === $email) {
            $response->addError(ErrorMessagesInterface::INVALID_EMAIL);
        }
        $password = $_REQUEST['password'] ?? null;
        if (null === $password) {
            $response->addError(ErrorMessagesInterface::INVALID_PASSWORD);
        }
        if ($response->hasError()) {
            return $this->formatResponse($response);
        }

        $user = $this->userService->findOneByEmail($email);
        if (null !== $user && $this->userService->login($user, $password)) {
            $response->addData('isRegistered', true);
            $response->addData('isLoggedIn', true);
            $response->addData('userUUID', (string) $user->getUUID());
        } else {
            $response->addData('isLoggedIn', false);
            $response->addErrors($this->userService->getErrorHandler()->getErrors());
        }

        return $this->formatResponse($response);
    }


    /**
     * @return string
     */
    public function signUp(): string
    {
        $response = new Response();
        $user = $this->userService->createFromArray($_REQUEST);
        if (true === $this->userService->save($user) && true === $this->userService->login($user, $_REQUEST['password'])) {
            $response->addData('isRegistered', true);
            $response->addData('isLoggedIn', true);
            $response->addData('userUUID', (string) $user->getUUID());
        } else {
            $response->addData('isLoggedIn', false);
            $response->addErrors($this->userService->getErrorHandler()->getErrors());
        }
        return $this->formatResponse($response);
    }


    /**
     * @return string
     */
    public function logout(): string
    {
        $this->userService->logout();
        $response = new Response();
        return $this->formatResponse($response);
    }
}
