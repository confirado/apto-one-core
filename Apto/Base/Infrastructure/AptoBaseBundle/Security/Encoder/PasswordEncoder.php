<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Security\Encoder;

use Apto\Base\Domain\Core\Service\Exception\BcryptCostRangeException;
use Apto\Base\Domain\Core\Service\Exception\PasswordValidationException;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Apto\Base\Domain\Core\Service\PasswordEncoder as CorePasswordEncoder;

class PasswordEncoder implements PasswordHasherInterface
{
    private CorePasswordEncoder $corePasswordEncoder;
    
    /**
     * PasswordEncoder constructor.
     * @param int $cost
     * @throws BcryptCostRangeException
     */
    public function __construct(int $cost = 10)
    {
        $this->corePasswordEncoder = new CorePasswordEncoder($cost);
    }

    /**
     * @param string $plainPassword
     * @return string
     * @throws PasswordValidationException
     */
    public function hash(string $plainPassword): string
    {
        return $this->corePasswordEncoder->encodePassword($plainPassword);
    }

    /**
     * @param string $hashedPassword
     * @param string $plainPassword
     * @return bool
     */
    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        return $this->corePasswordEncoder->isPasswordValid($hashedPassword, $plainPassword);
    }

    /**
     * @param string $hashedPassword
     * @return bool
     */
    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }
}