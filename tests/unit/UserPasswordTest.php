<?php

use PHPUnit\Framework\TestCase;
use HybridLogin\User\UserPassword;


/**
 * Class UserTest
 * @property UserPassword $userPassword
 */
final class UserPasswordTest extends TestCase
{
    protected $userPassword;


    public function setUp()
    {
        $this->userPassword = new UserPassword();
    }


    public function testInstanceOfUserPasswordClass(): void
    {
        $this->assertInstanceOf(UserPassword::class, $this->userPassword);
    }


    public function testGetRestrictions(): void
    {
        $passwordRestrictions = UserPassword::getRestrictions();
        foreach ($passwordRestrictions as $passwordRestriction) {
            $this->assertArrayHasKey('regularExpression', $passwordRestriction);
            $this->assertArrayHasKey('message', $passwordRestriction);
        }
    }

}
