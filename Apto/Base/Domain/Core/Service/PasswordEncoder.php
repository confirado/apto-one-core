<?php

namespace Apto\Base\Domain\Core\Service;

use Apto\Base\Domain\Core\Service\Exception\PasswordValidationException;
use Apto\Base\Domain\Core\Service\Exception\BcryptCostRangeException;

class PasswordEncoder
{
    const MIN_PASSWORD_LENGTH = 8;
    const MAX_PASSWORD_LENGTH = 72;

    /**
     * @var string
     */
    private $cost = 10;

    /**
     * PasswordEncoder constructor.
     * @param int $cost
     * @throws BcryptCostRangeException
     */
    public function __construct(int $cost = 10)
    {
        $cost = (int) $cost;
        if ($cost < 4 || $cost > 31) {
            throw new BcryptCostRangeException('Cost must be in the range of 4-31.');
        }

        $this->cost = $cost;
    }

    /**
     * @param $raw
     * @param bool $salt
     * @return bool|string
     * @throws PasswordValidationException
     */
    public function encodePassword($raw, $salt = false)
    {
        if ($this->isPasswordTooLong($raw) || $this->isPasswordTooShort($raw)) {
            throw new PasswordValidationException('Password validation failed');
        }

        $options = array('cost' => $this->cost);

        if ($salt) {
            // Ignore $salt, the auto-generated one is always the best
            $options = array('salt' => $salt);
        }

        return password_hash($raw, PASSWORD_BCRYPT, $options);
    }

    /**
     * @param string $encoded
     * @param string $raw
     * @return bool
     */
    public function isPasswordValid(string $encoded, string $raw): bool
    {
        return !$this->isPasswordTooLong($raw) && password_verify($raw, $encoded) && !$this->isPasswordTooShort($raw);
    }

    /**
     * Checks if the password is too long.
     *
     * @param string $password The password to check
     *
     * @return bool true if the password is too long, false otherwise
     */
    protected function isPasswordTooLong($password)
    {
        return strlen($password) > static::MAX_PASSWORD_LENGTH;
    }

    /**
     * Checks if the password is too long.
     *
     * @param string $password The password to check
     *
     * @return bool true if the password is too long, false otherwise
     */
    protected function isPasswordTooShort($password)
    {
        return strlen($password) < static::MIN_PASSWORD_LENGTH;
    }
}