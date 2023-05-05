<?php

namespace Apto\Base\Application\Backend\Commands\AclEntry;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAclPermission implements AclPermission, CommandInterface
{
    /**
     * @var mixed
     */
    private $shopId;

    /**
     * @var mixed
     */
    private $roleId;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var mixed
     */
    private $entityId;

    /**
     * @var string
     */
    private $entityField;

    /**
     * @var array
     */
    private $permissions;

    /**
     * AddAclPermission constructor.
     * @param mixed $shopId
     * @param mixed $roleId
     * @param string $entityClass
     * @param mixed $entityId
     * @param string $entityField
     * @param array $permissions
     */
    public function __construct($shopId, $roleId, string $entityClass, $entityId, $entityField, array $permissions)
    {
        $this->shopId = $shopId;
        $this->roleId = $roleId;
        $this->entityClass = $entityClass;
        $this->entityId = $entityId;
        $this->entityField = $entityField;
        $this->permissions = $permissions;
    }

    /**
     * @return mixed
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @return string|null
     */
    public function getEntityField()
    {
        return $this->entityField;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }
}