<?php

namespace Apto\Base\Application\Backend\Query\AclEntry;

interface AclEntryFinder
{
    /**
     * @param string $aclClass
     * @return mixed
     */
    public function findByAclClass(string $aclClass);

    /**
     * @param string $shopId
     * @return mixed
     */
    public function findByShopId(string $shopId);

    /**
     * @param string $roleIdentifier
     * @param string $modelClass
     * @param string|null $shopId
     * @param mixed|null $entityId
     * @param string|null $fieldName
     * @return array
     */
    public function findByShopRoleIdentity(string $roleIdentifier, string $modelClass, $shopId = null, $entityId = null, $fieldName = null);
}