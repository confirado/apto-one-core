<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Security\User;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, EquatableInterface, \Serializable, AptoTokenUserInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var array
     */
    private $userRoles;

    /**
     * @var string|null
     */
    private $userLicenceHash;

    /**
     * @var \DateTimeImmutable|null
     */
    private $userLicenceSignatureTimestamp;

    /**
     * @var string|null
     */
    private $apiKey;

    /**
     * User constructor.
     * @param $id
     * @param $username
     * @param $password
     * @param $salt
     * @param $active
     * @param $userRoles
     * @param $userLicenceHash
     * @param $userLicenceSignatureTimestamp
     * @param $apiKey
     */
    public function __construct($id, $username, $password, $salt, $active, $userRoles, $userLicenceHash, $userLicenceSignatureTimestamp, $apiKey)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->active = $active;
        $this->userRoles = $userRoles;
        $this->userLicenceHash = $userLicenceHash;
        $this->userLicenceSignatureTimestamp = $userLicenceSignatureTimestamp;
        $this->apiKey = $apiKey;
    }

    /**
     * @param array $aptoUser
     * @return UserInterface
     * @throws \Exception
     */
    public static function createFromBaseUser(array $aptoUser, $aptoUserRoles)
    {
        $id = isset($aptoUser['id']) ? $aptoUser['id'] : null;
        $username = isset($aptoUser['username']) ? $aptoUser['username'] : null;
        $password = isset($aptoUser['password']) ? $aptoUser['password'] : null;
        $salt = isset($aptoUser['salt']) ? $aptoUser['salt'] : null;
        $active = isset($aptoUser['active']) ? $aptoUser['active'] : false;
        $userRoles = $aptoUserRoles;
        $userLicenceHash = isset($aptoUser['userLicenceHash']) ? $aptoUser['userLicenceHash'] : '';
        $userLicenceSignatureTimestamp = isset($aptoUser['userLicenceSignatureTimestamp']) ? new \DateTimeImmutable($aptoUser['userLicenceSignatureTimestamp']) : '';
        $apiKey = isset($aptoUser['apiKey']) ? $aptoUser['apiKey'] : null;

        $user = new self(
            $id,
            $username,
            $password,
            $salt,
            $active,
            $userRoles,
            $userLicenceHash,
            $userLicenceSignatureTimestamp,
            $apiKey
        );

        return $user;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the identifier for this user (e.g. its username or email address).
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return mixed
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return mixed
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->active;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->userRoles;
    }

    /**
     * @return string|null
     */
    public function getUserLicenceHash()
    {
        return $this->userLicenceHash;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUserLicenceSignatureTimestamp()
    {
        return $this->userLicenceSignatureTimestamp;
    }

    /**
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param UserInterface $user
     * @return boolean
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        if ($this->apiKey !== $user->getApiKey()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
            $this->active,
            $this->userLicenceHash,
            $this->userLicenceSignatureTimestamp,
            $this->apiKey
        ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
            $this->active,
            $this->userLicenceHash,
            $this->userLicenceSignatureTimestamp,
            $this->apiKey
        ) = unserialize($serialized);
    }
}