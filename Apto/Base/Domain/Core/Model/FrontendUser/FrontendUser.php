<?php

namespace Apto\Base\Domain\Core\Model\FrontendUser;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Email;

class FrontendUser extends AptoAggregate
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
     * @var string
     */
    protected $externalCustomerGroupId;

    /**
     * @var string
     */
    protected $customerNumber;

    /**
     * FrontendUser constructor.
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
            new FrontendUserAdded($id)
        );
        $this
            ->setUsername($username)
            ->setPassword($password)
            ->setEmail($email)
            ->setActive(true)
            ->setExternalCustomerGroupId('')
            ->setCustomerNumber('');
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
     * @return FrontendUser
     */
    public function setActive($active): FrontendUser
    {
        if ($this->active === $active) {
            return $this;
        }
        $this->active = $active;
        $this->publish(
            new FrontendUserActiveUpdated(
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
     * @return FrontendUser
     * @throws InvalidUserNameException
     */
    public function setUsername(UserName $username): FrontendUser
    {
        if (null !== $this->username && $this->username->equals($username)) {
            return $this;
        }

        $this->username = $username;
        $this->publish(
            new FrontendUserUsernameUpdated(
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
     * @return FrontendUser
     */
    public function setPassword($password): FrontendUser
    {
        if ($this->password === $password) {
            return $this;
        }
        $this->password = $password;
        $this->publish(
            new FrontendUserPasswordUpdated(
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
     * @return FrontendUser
     */
    public function setEmail(Email $email): FrontendUser
    {
        if ($this->email === $email) {
            return $this;
        }
        $this->email = $email;
        $this->publish(
            new FrontendUserEmailUpdated(
                $this->getId(),
                $this->getEmail()
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalCustomerGroupId(): string
    {
        return $this->externalCustomerGroupId;
    }

    /**
     * @param string $externalCustomerGroupId
     * @return FrontendUser
     */
    public function setExternalCustomerGroupId(string $externalCustomerGroupId): FrontendUser
    {
        if ($this->externalCustomerGroupId === $externalCustomerGroupId) {
            return $this;
        }

        $this->externalCustomerGroupId = $externalCustomerGroupId;

        $this->publish(
            new FrontendUserExternalCustomerGroupIdUpdated(
                $this->getId(),
                $this->externalCustomerGroupId
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }

    /**
     * @param string $customerNumber
     * @return FrontendUser
     */
    public function setCustomerNumber(string $customerNumber): FrontendUser
    {
        if ($this->customerNumber === $customerNumber) {
            return $this;
        }

        $this->customerNumber = $customerNumber;

        $this->publish(
            new FrontendUserCustomerNumberUpdated(
                $this->getId(),
                $this->getCustomerNumber()
            )
        );
        return $this;
    }
}
