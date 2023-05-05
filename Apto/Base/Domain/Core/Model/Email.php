<?php

namespace Apto\Base\Domain\Core\Model;

/**
 * @todo: property email change to string after an object is loaded from db, all models who use Email object are affected
 * Class Email
 * @package Apto\Base\Domain\Core\Model
 */
class Email implements \JsonSerializable
{
    /**
     * @var string
     */
    protected string $email;

    /**
     * Email constructor.
     * @param string $email
     * @throws EmailValidationException
     */
    public function __construct(string $email)
    {
        $this->setEmail($email);
    }

    /**
     * @param string $email
     * @throws EmailValidationException
     */
    protected function setEmail(string $email)
    {
        if (false === $this->validate($email)) {
            throw new EmailValidationException('Email validation failed');
        }
        $this->email = strtolower($email);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return mixed
     */
    protected function validate(string $email)
    {
        $emailExplode = explode('@', $email);

        if (count($emailExplode) !== 2) {
            return false;
        }

        $email = $emailExplode[0] . '@' . idn_to_ascii($emailExplode[1], IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);

        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param Email $email
     * @return bool
     */
    public function equals(Email $email): bool
    {
        return $this->getEmail() === $email->getEmail();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
