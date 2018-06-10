<?php

use PHPUnit\Framework\TestCase;
use HybridLogin\User\UserController;
use HybridLogin\User\UserService;


/**
 * Class UserControllerTest
 */
final class UserControllerTest extends TestCase
{
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
        $container = new \HybridLogin\Container(new \HybridLogin\Model\Repository\FooRepositoryHandler());
        $controller = new \HybridLogin\Controller\Controller($container, ['route' => 'user']);
        $this->userService = $container->getUserService();
        $this->controller = $controller->getEffectiveController();
    }

    public function testControllerIsInstanceOfUserController(): void
    {
        $this->assertInstanceOf(UserController::class, $this->controller);
    }

    public function testIsRegisteredNullEmail(): void
    {
        $_REQUEST['email'] = null;
        $this->controller->isRegistered();
        $this->assertEquals(\HybridLogin\Error\ErrorMessagesInterface::INVALID_EMAIL,
            $this->controller->getParentController()->getResponse()->getLastError());
    }

    public function testSignInNullEmail(): void
    {
        $_REQUEST['email'] = null;
        $this->controller->signIn();
        $this->assertArraySubset([\HybridLogin\Error\ErrorMessagesInterface::INVALID_EMAIL],
            $this->controller->getParentController()->getResponse()->getErrors());
    }

    public function testIsNotRegistered(): void
    {
        $_REQUEST['email'] = 'not@notnot.not';
        $this->controller->isRegistered();
        $this->assertFalse($this->controller->getParentController()->getResponse()->getNode('isRegistered'));
    }

    public function testSignInWithInvalidPassword(): void
    {
        $_REQUEST['email'] = 'not@notnot.not';
        $this->controller->signIn();
        $this->assertEquals(\HybridLogin\Error\ErrorMessagesInterface::INVALID_PASSWORD,
            $this->controller->getParentController()->getResponse()->getLastError());
    }

    public function testSignUpAndIsRegisteredAndSignIn(): void
    {
        $_REQUEST['email'] = 'not@notnot.not';
        $_REQUEST['password'] = 'ASDasd123';
        $this->controller->signUp();
        $this->assertTrue($this->controller->getParentController()->getResponse()->getNode('isLoggedIn'));
        $this->assertTrue($this->controller->getParentController()->getResponse()->getNode('isRegistered'));

        $this->controller->isRegistered();
        $this->assertTrue($this->controller->getParentController()->getResponse()->getNode('isRegistered'));

        $this->controller->signIn();
        $this->assertTrue($this->controller->getParentController()->getResponse()->getNode('isLoggedIn'));
        $this->assertEquals($_SESSION['loggedUserEmail'], $_REQUEST['email']);

        $_REQUEST['password'] = 'ZZZnot999';
        $this->controller->signIn();
        $this->assertFalse($this->controller->getParentController()->getResponse()->getNode('isLoggedIn'));
    }

    public function testSignUpAndLoginAndLogout(): void
    {
        $_REQUEST['email'] = 'not@notnot.not';
        $_REQUEST['password'] = 'ASDasd123';
        $this->controller->signUp();
        $this->assertTrue($this->controller->getParentController()->getResponse()->getNode('isLoggedIn'));
        $this->assertTrue($this->controller->getParentController()->getResponse()->getNode('isRegistered'));

        $this->controller->isRegistered();
        $this->assertTrue($this->controller->getParentController()->getResponse()->getNode('isRegistered'));

        $this->controller->signIn();
        $this->assertTrue($this->controller->getParentController()->getResponse()->getNode('isLoggedIn'));
        $this->assertEquals($_SESSION['loggedUserEmail'], $_REQUEST['email']);

        $this->controller->logout();
        $this->assertNull($_SESSION['loggedUserEmail']);
    }

    public function testSignUpFailure(): void
    {
        $_REQUEST['email'] = 'not@notnot.not';
        $_REQUEST['password'] = 'ASDasd';
        $this->controller->signUp();
        $this->assertFalse($this->controller->getParentController()->getResponse()->getNode('isLoggedIn'));
        $this->assertContains(\HybridLogin\User\UserPassword::MUST_HAVE_8_CHAR_OR_MORE,$this->controller->getParentController()->getResponse()->getErrors());
    }

}
