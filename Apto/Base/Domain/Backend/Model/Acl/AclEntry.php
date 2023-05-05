<?php

namespace Apto\Base\Domain\Backend\Model\Acl;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Backend\Model\UserRole\UserRoleIdentifier;

class AclEntry
{
    /**
     * @var mixed
     */
    protected $surrogateId;

    /**
     * @var string|null
     */
    protected $shop;

    /**
     * @var UserRoleIdentifier
     */
    protected $role;

    /**
     * @var AclIdentity
     */
    protected $identity;

    /**
     * @var AclMask
     */
    protected $mask;

    /**
     * AclEntry constructor.
     * @param AptoUuid|null $shop
     * @param UserRoleIdentifier $role
     * @param AclIdentity $identity
     * @param AclMask $mask
     */
    public function __construct($shop, UserRoleIdentifier $role, AclIdentity $identity, AclMask $mask)
    {
        $this->shop = $shop;
        $this->role = $role;
        $this->identity = $identity;
        $this->mask = $mask;
    }

    /**
     * cant use mixed here as return type because doctrine throws an error when return type dont match the actual mapping
     * @return mixed
     */
    public function getSurrogateId()
    {
        return $this->surrogateId;
    }

    /**
     * @return AptoUuid|null
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @return UserRoleIdentifier
     */
    public function getRole(): UserRoleIdentifier
    {
        return $this->role;
    }

    /**
     * @return AclIdentity
     */
    public function getIdentity(): AclIdentity
    {
        return $this->identity;
    }

    /**
     * @return AclMask
     */
    public function getMask(): AclMask
    {
        return $this->mask;
    }

    /**
     * @param AclMask $mask
     * @return AclEntry
     */
    public function setMask(AclMask $mask): AclEntry
    {
        $this->mask = $mask;
        return $this;
    }
}