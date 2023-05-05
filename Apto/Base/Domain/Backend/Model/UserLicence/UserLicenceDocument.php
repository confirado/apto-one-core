<?php

namespace Apto\Base\Domain\Backend\Model\UserLicence;

class UserLicenceDocument
{
    const SECRET = 'egCAULj94c5LQLlv9MOdEh5hkhBfOyC';

    /**
     * @var string
     */
    private $licenceTitle;

    /**
     * @var string
     */
    private $licenceText;

    /**
     * @var string
     */
    private $username;

    /**
     * UserLicenceHashValue constructor.
     * @param string $licenceTitle
     * @param string $licenceText
     * @param string $username
     */
    public function __construct(
        string $licenceTitle,
        string $licenceText,
        string $username
    ) {
        $this->licenceTitle = $licenceTitle;
        $this->licenceText = $licenceText;
        $this->username = $username;
    }

    /**
     * @param \DateTimeImmutable|null $timestamp
     * @return UserLicenceSignature
     * @throws \Exception
     */
    public function sign(\DateTimeImmutable $timestamp = null): UserLicenceSignature
    {
        if (null === $timestamp) {
            $timestamp = new \DateTimeImmutable();
        }
        return new UserLicenceSignature(
            password_hash(
                $this->generateDocument($timestamp),
                PASSWORD_DEFAULT
            ),
            $timestamp
        );
    }

    /**
     * @param UserLicenceSignature $userLicenceSignature
     * @return bool
     * @throws \Exception
     */
    public function validateSignature(UserLicenceSignature $userLicenceSignature): bool
    {
        return password_verify(
            $this->generateDocument(
                $userLicenceSignature->getTimestamp()
            ),
            $userLicenceSignature->getHash()
        );
    }

    /**
     * @param \DateTimeImmutable $timestamp
     * @return string
     * @throws \Exception
     */
    private function generateDocument(\DateTimeImmutable $timestamp): string
    {
        $value = implode("\n", [
            $timestamp->getTimestamp(),
            $this->username,
            $this->licenceTitle,
            $this->licenceText
        ]);

        $document = sha1($value) . self::SECRET;

        if (strlen($document) > 72) {
            throw new \Exception('\'$document\' must NOT exceed 72 chars due to limitations in BlowFish');
        }

        return $document;
    }
}