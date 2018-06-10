<?php

use PHPUnit\Framework\TestCase;
use HybridLogin\User\User;
use HybridLogin\User\UserService;

/**
 * Class UserServiceTest
 * @property UserService $userService
 */
final class UserServiceTest extends TestCase
{
    protected $userService;


    public function setUp()
    {
        $container = new \HybridLogin\Container(new \HybridLogin\Model\Repository\FooRepositoryHandler());
        $this->userService = $container->getUserService();

    }


    public function testInstanceOfUserService(): void
    {
        $this->assertInstanceOf(UserService::class, $this->userService);
    }

    public function testCreateUser(): void
    {
        $userData = [
            'email' => 'viniciusk@gmail.com',
            'password' => 'Qazwsx12'
        ];
        $user = $this->userService->createFromArray($userData, false);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testFailToCreateUserBadFormat(): void
    {
        $userData = [
            'x' => 'viniciusk@gmail.com',
            'password' => 'Qazwsx12'
        ];
        $user = $this->userService->createFromArray($userData, false);
        $this->assertFalse($user->validate());
        $errors = $this->userService->getErrorHandler()->getErrors();
        $this->assertContains(\HybridLogin\Error\ErrorMessagesInterface::INVALID_EMAIL, $errors);
    }

    public function testFailToCreateUserShortPassword(): void
    {
        $userData = [
            'email' => 'viniciusk@gmail.com',
            'password' => 'Qasx12'
        ];
        $user = $this->userService->createFromArray($userData, false);
        $this->assertFalse($user->isPasswordSet());
        $user->validate();
        $errors = $this->userService->getErrorHandler()->getErrors();
        $this->assertContains(\HybridLogin\Error\ErrorMessagesInterface::INVALID_PASSWORD, $errors);
        $this->assertContains(\HybridLogin\User\UserPassword::MUST_HAVE_8_CHAR_OR_MORE, $errors);
    }

    public function testFailToCreateUserNoNumber(): void
    {
        $userData = [
            'email' => 'viniciusk@gmail.com',
            'password' => 'QassadasdxWW'
        ];
        $user = $this->userService->createFromArray($userData, false);
        $this->assertFalse($user->isPasswordSet());
        $user->validate();
        $errors = $this->userService->getErrorHandler()->getErrors();
        $this->assertContains(\HybridLogin\Error\ErrorMessagesInterface::INVALID_PASSWORD, $errors);
        $this->assertContains(\HybridLogin\User\UserPassword::MUST_HAVE_ONE_NUMBER, $errors);
    }

    public function testFailToCreateUserNoUpperCase(): void
    {
        $userData = [
            'email' => 'viniciusk@gmail.com',
            'password' => 'asdasd123123asd'
        ];
        $user = $this->userService->createFromArray($userData, false);
        $this->assertFalse($user->isPasswordSet());
        $user->validate();
        $errors = $this->userService->getErrorHandler()->getErrors();
        $this->assertContains(\HybridLogin\Error\ErrorMessagesInterface::INVALID_PASSWORD, $errors);
        $this->assertContains(\HybridLogin\User\UserPassword::MUST_HAVE_ONE_UPPER_CASE, $errors);    }

    public function testFailToCreateUserNoLowerCase(): void
    {
        $userData = [
            'email' => 'viniciusk@gmail.com',
            'password' => 'ASD123ASD1213'
        ];
        $user = $this->userService->createFromArray($userData, false);
        $this->assertFalse($user->isPasswordSet());
        $user->validate();
        $errors = $this->userService->getErrorHandler()->getErrors();
        $this->assertContains(\HybridLogin\Error\ErrorMessagesInterface::INVALID_PASSWORD, $errors);
        $this->assertContains(\HybridLogin\User\UserPassword::MUST_HAVE_ONE_LOWER_CASE, $errors);
    }

    public function testFailToCreateUserNoPassword(): void
    {
        $userData = [
            'email' => 'viniciusk@gmail.com',
            'password' => null
        ];
        $user = $this->userService->createFromArray($userData, false);
        $this->assertFalse($user->validate());
        $errors = $this->userService->getErrorHandler()->getErrors();
        $this->assertContains(\HybridLogin\Error\ErrorMessagesInterface::INVALID_PASSWORD, $errors);
    }

    public function testCreateInactiveUserWithId(): void
    {
        $userData = [
            'uuid' => 1,
            'email' => 'viniciusk@gmail.com',
            'password' => 'ASDasdD12ss13'
        ];
        $user = $this->userService->createFromArray($userData, false);
        $this->assertTrue($user->isPasswordSet());
        $this->assertEquals(1, $user->getUUID());
        $this->assertFalse($user->isActive());
    }

    public function testCreateActiveUserWithId(): void
    {
        $userData = [
            'uuid' => 1,
            'email' => 'viniciusk@gmail.com',
            'password' => 'ASDasdD12ss13',
            'active' => 1,
        ];
        $user = $this->userService->createFromArray($userData, false);
        $this->assertTrue($user->isPasswordSet());
        $this->assertEquals(1, $user->getUUID());
        $this->assertTrue($user->isActive());
    }

    public function testCreateUserFromDatabase(): void
    {
        $userData = [
            'uuid' => 10,
            'email' => 'viniciusk@gmail.com',
            'password' => 'cfeacb9aef45c08a14de037cca8b084bbb9f0450689cdd0db0f62cc49cc80e78',
            'active' => 1,
        ];
        $user = $this->userService->createFromArray($userData, true);
        $this->assertTrue($user->isPasswordSet());
        $this->assertEquals(10, $user->getUUID());
        $this->assertTrue($user->isActive());
    }
}
