<?php

use PHPUnit\Framework\TestCase;
use HybridLogin\User\User;


/**
 * Class UserTest
 * @property User $user
 * @property \HybridLogin\User\UserService $userService
 */
final class UserTest extends TestCase
{
    protected $user;
    protected $userService;


    public function setUp()
    {
        $container = new \HybridLogin\Container(new \HybridLogin\Model\Repository\FooRepositoryHandler());
        $this->userService = $container->getUserService();
        $this->user = $this->userService->create();
    }


    public function testInstanceOfUserClass(): void
    {
        $this->assertInstanceOf(User::class, $this->user);
    }


    public function testIdNullByDefault(): void
    {
        $this->assertNull($this->user->getUUID());
    }

    public function testSetId(): void
    {
        $this->user->setUUID(2);
        $this->assertEquals(2, $this->user->getUUID());
    }

    public function testSetEmail(): void
    {
        $validEmail = 'vinicius@gmail.com';
        $this->user->setEmail($validEmail);
        $this->assertEquals($this->user->getEmail(), $validEmail); // valid email

        $invalidEmail = 'invalid@dddd';
        $this->user->setEmail($invalidEmail);
        $this->assertNull($this->user->getEmail()); // null

        $invalidEmail = 'invalid@dddd';
        $this->user = $this->userService->create();
        $this->user->setEmail($invalidEmail);
        $this->assertEquals($this->user->getEmail(), null); // remains null
    }

    public function testGetEmail(): void
    {
        $validEmail = 'vinicius@gmail.com';
        $this->user->setEmail($validEmail);
        $this->assertEquals($this->user->getEmail(), $validEmail); // valid email
    }


    public function testSetValidPassword(): void
    {
        $validPassword = 'Qazwsx12';
        $this->user->setPassword($validPassword);
        $this->assertTrue($this->user->isPasswordSet());

        $validPassword = 'asd23ASss';
        $this->user = $this->userService->create();
        $this->user->setPassword($validPassword);
        $this->assertTrue($this->user->isPasswordSet());
    }


    public function testSetInvalidPassword(): void
    {
        $invalidPassword = 'Qazwsx';
        $this->user = $this->userService->create();
        try {
            $this->user->setPassword($invalidPassword);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), HybridLogin\User\UserPassword::MUST_HAVE_8_CHAR_OR_MORE);
        }
        $this->assertFalse($this->user->isPasswordSet());

        $invalidPassword = 'asdasdas';
        $this->user = $this->userService->create();
        try {
            $this->user->setPassword($invalidPassword);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), HybridLogin\User\UserPassword::MUST_HAVE_ONE_NUMBER);
        }
        $this->assertFalse($this->user->isPasswordSet());
    }


    public function testIsInactiveByDefault(): void
    {
        $this->assertFalse($this->user->isActive());
    }

    public function testSetActive(): void
    {
        $this->user->setActive(true);
        $this->assertTrue($this->user->isActive());
    }

    public function testSetInactive(): void
    {
        $this->user->setActive(true);
        $this->assertTrue($this->user->isActive());
        $this->user->setActive(false);
        $this->assertFalse($this->user->isActive());
    }

    public function testGetUserAttributes(): void
    {
        $this->assertEquals(
            [
                'uuid' => null,
                'email' => null,
                'password' => null,
                'active' => false,
            ],
            $this->user->getAttributes()
        );

        $this->user->setEmail('xurulas@example.com');
        $this->user->setActive(true);

        $this->assertEquals(
            [
                'uuid' => null,
                'email' => 'xurulas@example.com',
                'password' => null,
                'active' => true,
            ],
            $this->user->getAttributes()
        );
    }

    public function testValidateTrue(): void
    {
        $this->user->setEmail('xurulas@example.com');
        $this->user->setPassword('ASD123asd');
        $this->assertTrue($this->user->validate());
    }

    public function testValidateFalse(): void
    {
        $this->user->setEmail('xurulasexample.com');
        $this->user->setPassword('ASD123asd');
        $this->assertFalse($this->user->validate());
    }

}
