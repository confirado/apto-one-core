<?php

namespace Apto\Base\Domain\Backend\Model\Acl;

interface AclEntryRepository
{
    /**
     * @param AclEntry $model
     */
    public function update(AclEntry $model);

    /**
     * @param AclEntry $model
     */
    public function add(AclEntry $model);

    /**
     * @param AclEntry $model
     */
    public function remove(AclEntry $model);

    /**
     * @param string $shopId
     */
    public function removeByShopId(string $shopId);

    /**
     * @param string $id
     * @return AclEntry|null
     */
    public function findById($id);

    /**
     * @param string|null $shop
     * @param string $role
     * @param AclIdentity $identity
     * @return AclEntry|null
     */
    public function findByShopRoleIdentity($shop, string $role, AclIdentity $identity);
}