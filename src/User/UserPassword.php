<?php

namespace HybridLogin\User;

/**
 * Class UserPassword
 * @package HybridLogin\User
 */
final class UserPassword
{
    public const MUST_HAVE_8_CHAR_OR_MORE = 'Must have 8 chars or more.';
    public const MUST_HAVE_ONE_UPPER_CASE = 'Must have at least one UPPER CASE char.';
    public const MUST_HAVE_ONE_LOWER_CASE = 'Must have at least one lower case char.';
    public const MUST_HAVE_ONE_NUMBER = 'Must have at least one numb3r.';


    /**
     * TODO: convert stupid array into a Password Restriction object
     * 
     * @return array
     */
    public static function getRestrictions(): array
    {
        return [
            [
                'regularExpression' => '/.{8,}/',
                'message' => self::MUST_HAVE_8_CHAR_OR_MORE
            ],
            [
                'regularExpression' => '/[A-Z]+/',
                'message' => self::MUST_HAVE_ONE_UPPER_CASE
            ],
            [
                'regularExpression' => '/[a-z]+/',
                'message' => self::MUST_HAVE_ONE_LOWER_CASE
            ],
            [
                'regularExpression' => '/[\d]+/',
                'message' => self::MUST_HAVE_ONE_NUMBER
            ],
        ];
    }


    /**
     * @param string $password
     * @return string
     */
    public static function encryptPassword(?string $password): string
    {
        if (null === $password) {
            return null;
        }
        return hash('sha256', $password);
    }

}
