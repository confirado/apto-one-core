<?php

namespace Apto\Base\Domain\Core\Model\Customer;

class CustomerUserName implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $username;

    /**
     * UserName constructor.
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->setUsername($username);
    }

    /**
     * @param string $username
     * @return CustomerUserName
     * @throws InvalidCustomerUserNameException
     */
    protected function setUsername(string $username): CustomerUserName
    {
        $username = strtolower($username);

        if (null === $username) {
            throw new InvalidCustomerUserNameException('Null is not a valid username.');
        }

        if (strlen($username) < 4) {
            throw new InvalidCustomerUserNameException('A username must have at least 4 characters.');
        }

        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param CustomerUserName $username
     * @return bool
     */
    public function equals(CustomerUserName $username): bool
    {
        return (null !== $username) && ($this->getUsername() == $username->getUsername());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }

}