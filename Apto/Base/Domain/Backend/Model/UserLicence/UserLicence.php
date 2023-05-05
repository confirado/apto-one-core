<?php

namespace Apto\Base\Domain\Backend\Model\UserLicence;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Infrastructure\AptoBaseBackendBundle\Security\User\User;

class UserLicence extends AptoAggregate
{
    const SECRET = 'egCAULj94c5LQLlv9MOdEh5hkhBfOyC';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var \DateTimeImmutable
     */
    protected $validSince;


    /**
     * UserLicence constructor.
     * @param AptoUuid $id
     * @param string $title
     * @param string $text
     * @param \DateTimeImmutable $validSince
     */
    public function __construct(AptoUuid $id, string $title, string $text, \DateTimeImmutable $validSince)
    {
        parent::__construct($id);
        $this
            ->setTitle($title)
            ->setText($text)
            ->setValidSince($validSince);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return UserLicence
     */
    public function setTitle(string $title): UserLicence
    {
        if ($this->title == $title) {
            return $this;
        }
        $this->title = $title;
        $this->publish(
            new UserLicenceTitleUpdated(
                $this->getId(),
                $this->getTitle()
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return UserLicence
     */
    public function setText(string $text): UserLicence
    {
        if ($this->text == $text) {
            return $this;
        }
        $this->text = $text;
        $this->publish(
            new UserLicenceTextUpdated(
                $this->getId(),
                $this->getText()
            )
        );
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getValidSince(): \DateTimeImmutable
    {
        return $this->validSince instanceof \DateTime ? \DateTimeImmutable::createFromMutable($this->validSince) : $this->validSince;
    }

    /**
     * @param \DateTimeImmutable $validSince
     * @return UserLicence
     */
    public function setValidSince(\DateTimeImmutable $validSince): UserLicence
    {
        if ($this->validSince === $validSince) {
            return $this;
        }
        $this->validSince = $validSince;
        $this->publish(
            new UserLicenceValidSinceUpdated(
                $this->getId(),
                $this->getValidSince()
            )
        );
        return $this;
    }
}