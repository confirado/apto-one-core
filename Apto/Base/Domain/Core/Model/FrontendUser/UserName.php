<?php

namespace Apto\Base\Domain\Core\Model\FrontendUser;

class UserName implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $username;

    /**
     * UserName constructor.
     * @param string $username
     * @throws InvalidUserNameException
     */
    public function __construct(string $username)
    {
        $this->setUsername($username);
    }

    /**
     * @param string $username
     * @return UserName
     * @throws InvalidUserNameException
     */
    protected function setUsername(string $username): UserName
    {
        $username = strtolower($username);

        if (null === $username) {
            throw new InvalidUserNameException('Null is not a valid username.');
        }

        if (strlen($username) < 4) {
            throw new InvalidUserNameException('A username must have at least 4 characters.');
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
     * @param UserName $username
     * @return bool
     */
    public function equals(UserName $username): bool
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
