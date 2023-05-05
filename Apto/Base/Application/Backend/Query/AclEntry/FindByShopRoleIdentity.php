<?php

namespace Apto\Base\Application\Backend\Query\AclEntry;

use Apto\Base\Application\Core\QueryInterface;

class FindByShopRoleIdentity implements QueryInterface
{
    /**
     * @var string|null
     */
    private $shopId;

    /**
     * @var string
     */
    private $roleIdentifier;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var mixed|null
     */
    private $entityId;

    /**
     * @var string|null
     */
    private $fieldName;

    /**
     * FindByShopRoleIdentity constructor.
     * @param string $roleIdentifier
     * @param string $modelClass
     * @param string|null $shopId
     * @param mixed|null $entityId
     * @param string|null $fieldName
     */
    public function __construct(string $roleIdentifier, string $modelClass, $shopId = null, $entityId = null, $fieldName = null)
    {
        $this->shopId = $shopId;
        $this->roleIdentifier = $roleIdentifier;
        $this->modelClass = $modelClass;
        $this->entityId = $entityId;
        $this->fieldName = $fieldName;
    }

    /**
     * @return string|null
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getRoleIdentifier(): string
    {
        return $this->roleIdentifier;
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * @return mixed|null
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @return string|null
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }
}