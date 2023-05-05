<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Security\FrontendUser;

use Exception;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FrontendUser implements UserInterface, EquatableInterface, \Serializable, AptoTokenFrontendUserInterface
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
     * @var bool
     */
    private $active;

    /**
     * User constructor.
     * @param $id
     * @param $username
     * @param $password
     * @param $active
     */
    public function __construct($id, $username, $password, $active)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->active = $active;
    }

    /**
     * @param array $aptoFrontendUser
     * @return UserInterface
     * @throws Exception
     */
    public static function createFromBaseUser(array $aptoFrontendUser)
    {
        $id = isset($aptoFrontendUser['id']) ? $aptoFrontendUser['id'] : null;
        $username = isset($aptoFrontendUser['username']) ? $aptoFrontendUser['username'] : null;
        $password = isset($aptoFrontendUser['password']) ? $aptoFrontendUser['password'] : null;
        $active = isset($aptoFrontendUser['active']) ? $aptoFrontendUser['active'] : false;

        $user = new self(
            $id,
            $username,
            $password,
            $active
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
     * @return string
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
        return null;
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
        return ['ROLE_LOGIN'];
    }

    /**
     * @param UserInterface $user
     * @return boolean
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof FrontendUser) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
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
            $this->active
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
            $this->active
            ) = unserialize($serialized);
    }
}
