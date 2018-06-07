<?php

use PHPUnit\Framework\TestCase;
use HybridLogin\User\UserController;
use HybridLogin\User\UserService;
use HybridLogin\User\User;


/**
 * Class UserControllerTest
 */
final class UserControllerTest extends TestCase
{
    /**
     * @var User $user
    protected $user;
     */

    /**
     * @var UserController $controller
     */
    protected $controller;

    /**
     * @var UserService $userService
     */
    protected $userService;


    public function setUp()
    {
        $container = new \HybridLogin\Container();
        $this->userService = $container->getUserService();
        $this->controller = new UserController($this->userService);
    }


    public function testControllerIsInstanceOfUserController(): void
    {
        $this->assertInstanceOf(UserController::class, $this->controller);
    }


    public function testIsRegisteredByEmailHasJsonResponse(): void
    {
        //$email = 'test@example.com';

        // todo: mock request

        $response = $this->controller->isRegistered();

        $this->assertJson($response);
    }

}
