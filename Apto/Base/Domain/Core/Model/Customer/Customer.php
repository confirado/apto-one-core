<?php

namespace Apto\Base\Domain\Core\Model\Customer;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Email;

class Customer extends AptoAggregate
{
    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var CustomerUserName
     */
    protected $username;

    /**
     * @var Email
     */
    protected $email;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var Gender
     */
    protected $gender;

    /**
     * @var AptoUuid
     */
    protected $shopId;

    /**
     * @var string
     */
    protected $externalId;

    /**
     * Customer constructor.
     * @param AptoUuid $id
     * @param CustomerUserName $username
     * @param Email $email
     * @param AptoUuid $shopId
     * @param string $externalId
     */
    public function __construct(AptoUuid $id,  CustomerUserName $username, Email $email, AptoUuid $shopId, string $externalId)
    {
        parent::__construct($id);
        $this->username = $username;
        $this->shopId = $shopId;
        $this->externalId = $externalId;
        $this->email = $email;
        $this->active = false;
        $this->gender = new Gender(Gender::UNKNOWN);
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
     * @return Customer
     */
    public function setActive(bool $active): Customer
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return CustomerUserName
     */
    public function getUsername(): CustomerUserName
    {
        return $this->username;
    }

    /**
     * @param CustomerUserName $username
     * @return Customer
     */
    public function setUsername(CustomerUserName $username): Customer
    {
        $this->username = $username;
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
     * @return Customer
     */
    public function setEmail(Email $email): Customer
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Customer
     */
    public function setFirstName(string $firstName): Customer
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return Customer
     */
    public function setLastName(string $lastName): Customer
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return Gender
     */
    public function getGender(): Gender
    {
        return $this->gender;
    }

    /**
     * @param Gender $gender
     * @return Customer
     */
    public function setGender(Gender $gender): Customer
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return AptoUuid
     */
    public function getShopId(): AptoUuid
    {
        return $this->shopId;
    }

    /**
     * @param AptoUuid $shopId
     * @return Customer
     */
    public function setShopId(AptoUuid $shopId): Customer
    {
        $this->shopId = $shopId;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return Customer
     */
    public function setExternalId(string $externalId = null): Customer
    {
        $this->externalId = $externalId;
        return $this;
    }
}