<?php

namespace Apto\Base\Domain\Backend\Model\UserLicence;

class UserLicenceSignature
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var \DateTimeImmutable
     */
    private $timestamp;

    /**
     * UserLicenceHash constructor.
     * @param string $hash
     * @param \DateTimeImmutable $timestamp
     */
    public function __construct(string $hash, \DateTimeImmutable $timestamp)
    {
        $this->hash = $hash;
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @param UserLicenceSignature $licenceSignature
     * @return bool
     */
    public function equals(UserLicenceSignature $licenceSignature): bool
    {
        return
            $this->getHash() === $licenceSignature->getHash() &&
            $this->getTimestamp() === $licenceSignature->getTimestamp();
    }
}