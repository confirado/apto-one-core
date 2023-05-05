<?php

namespace Apto\Base\Domain\Core\Model;

class EmailOptional
{
    /**
     * @var string|null
     */
    protected ?string $email;

    /**
     * @param string|null $email
     * @throws EmailValidationException
     */
    public function __construct(?string $email)
    {
        $this->setEmail($email);
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param EmailOptional $email
     * @return bool
     */
    public function equals(EmailOptional $email): bool
    {
        return $this->getEmail() === $email->getEmail();
    }

    /**
     * @param string|null $email
     * @return bool|mixed
     */
    protected function validate(?string $email)
    {
        if ($email === null) {
            return true;
        }

        $emailExplode = explode('@', $email);

        if (count($emailExplode) !== 2) {
            return false;
        }

        $email = $emailExplode[0] . '@' . idn_to_ascii($emailExplode[1], IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);

        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param string|null $email
     * @return void
     * @throws EmailValidationException
     */
    protected function setEmail(?string $email)
    {
        if (false === $this->validate($email)) {
            throw new EmailValidationException('Email validation failed');
        }

        if (null === $email) {
            $this->email = $email;
        } else {
            $this->email = strtolower($email);
        }
    }
}
