<?php

namespace Apto\Base\Domain\Backend\Model\User;

use Apto\Base\Domain\Backend\Model\UserLicence\UserLicenceSignature;
use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Email;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class User extends AptoAggregate
{
    /**
     * @var bool
     */
    protected $active;

    /**
     * @var UserName
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var Email
     */
    protected $email;

    /**
     * @var Collection
     */
    protected $userRoles;

    /**
     * @var string
     */
    protected $userLicenceHash;

    /**
     * @var \DateTimeImmutable
     */
    protected $userLicenceSignatureTimestamp;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var string
     */
    protected $rte;

    /**
     * @var string|null
     */
    protected $apiKey;

    /**
     * @var string|null
     */
    protected $apiOrigin;

    /**
     * User constructor.
     * @param AptoUuid $id
     * @param UserName $username
     * @param string $password
     * @param Email $email
     * @throws InvalidUserNameException
     */
    public function __construct(AptoUuid $id, UserName $username, string $password, Email $email)
    {
        parent::__construct($id);
        $this->publish(
            new UserAdded($id)
        );
        $this->userRoles = new ArrayCollection();
        $this
            ->setUsername($username)
            ->setPassword($password)
            ->setEmail($email);
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return User
     */
    public function setActive($active): User
    {
        if ($this->active === $active) {
            return $this;
        }
        $this->active = $active;
        $this->publish(
            new UserActiveUpdated(
                $this->getId(),
                $this->getActive()
            )
        );
        return $this;
    }

    /**
     * @return UserName
     */
    public function getUsername(): UserName
    {
        return $this->username;
    }

    /**
     * @param UserName $username
     * @return User
     * @throws InvalidUserNameException
     */
    public function setUsername(UserName $username): User
    {
        if (null !== $this->username && $this->username->equals($username)) {
            return $this;
        }

        $this->username = $username;
        $this->publish(
            new UserUsernameUpdated(
                $this->getId(),
                $this->getUsername()
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password): User
    {
        if ($this->password === $password) {
            return $this;
        }
        $this->password = $password;
        $this->publish(
            new UserPasswordUpdated(
                $this->getId()
            )
        );
        return $this;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @param Email $email
     * @return User
     */
    public function setEmail(Email $email): User
    {
        if ($this->email === $email) {
            return $this;
        }
        $this->email = $email;
        $this->publish(
            new UserEmailUpdated(
                $this->getId(),
                $this->getEmail()
            )
        );
        return $this;
    }

    /**
     * @return Collection
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    /**
     * @param $userRoles
     * @return User
     */
    public function setUserRoles(Collection $userRoles): User
    {
        if (!$this->hasCollectionChanged($this->getUserRoles(), $userRoles)) {
            return $this;
        }

        $this->userRoles = $userRoles;
        $this->publish(
            new UserUserRolesUpdated(
                $this->getId(),
                $this->getCollectionIds($this->getUserRoles())
            )
        );
        return $this;
    }

    /**
     * @return UserLicenceSignature|null
     */
    public function getUserLicenceSignature()
    {
        if (
            !$this->userLicenceHash ||
            !$this->userLicenceSignatureTimestamp
        ) {
            return null;
        }

        return new UserLicenceSignature(
            $this->userLicenceHash,
            $this->userLicenceSignatureTimestamp
        );
    }

    /**
     * @param UserLicenceSignature $userLicenceSignature
     * @return User
     */
    public function setUserLicenceSignature(UserLicenceSignature $userLicenceSignature): User
    {
        $currentSignature = null;
        if ($this->userLicenceHash && $this->userLicenceSignatureTimestamp) {
            $currentSignature = new UserLicenceSignature(
                $this->userLicenceHash,
                $this->userLicenceSignatureTimestamp instanceof \DateTime ? \DateTimeImmutable::createFromMutable($this->userLicenceSignatureTimestamp) : $this->userLicenceSignatureTimestamp
            );
        }

        if ($currentSignature && $userLicenceSignature->equals($currentSignature)) {
            return $this;
        }

        $this->userLicenceHash = $userLicenceSignature->getHash();
        $this->userLicenceSignatureTimestamp = $userLicenceSignature->getTimestamp();

        $this->publish(
            new UserUserLicenceSignatureUpdated(
                $this->getId()
            )
        );
        return $this;
    }


    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     * @return User
     */
    public function setSalt($salt): User
    {
        if ($this->salt === $salt) {
            return $this;
        }
        $this->salt = $salt;
        $this->publish(
            new UserSaltUpdated(
                $this->getId()
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getRte(): string
    {
        return $this->rte;
    }

    /**
     * @param string $rte
     * @return User
     */
    public function setRte(string $rte): User
    {
        if ($this->rte === $rte) {
            return $this;
        }

        $this->rte = $rte;
        $this->publish(
            new UserRteUpdated(
                $this->getId(),
                $rte
            )
        );
        return $this;
    }

    /**
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return User
     */
    public function setApiKey($apiKey): User
    {
        if ($this->apiKey === $apiKey) {
            return $this;
        }

        $this->apiKey = $apiKey;
        $this->publish(
            new UserApiKeyUpdated(
                $this->getId(),
                $apiKey
            )
        );
        return $this;
    }

    /**
     * @return string|null
     */
    public function getApiOrigin()
    {
        return $this->apiOrigin;
    }

    /**
     * @param string $apiOrigin
     * @return User
     */
    public function setApiOrigin($apiOrigin): User
    {
        if ($this->apiOrigin === $apiOrigin) {
            return $this;
        }

        $this->apiOrigin = $apiOrigin;
        $this->publish(
            new UserApiOriginUpdated(
                $this->getId(),
                $apiOrigin
            )
        );
        return $this;
    }
}