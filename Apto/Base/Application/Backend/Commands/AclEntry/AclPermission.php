<?php

namespace Apto\Base\Application\Backend\Commands\AclEntry;

interface AclPermission
{
    /**
     * @return mixed
     */
    public function getShopId();

    /**
     * @return mixed
     */
    public function getRoleId();

    /**
     * @return string
     */
    public function getEntityClass(): string;

    /**
     * @return mixed
     */
    public function getEntityId();

    /**
     * @return string|null
     */
    public function getEntityField();

    /**
     * @return array
     */
    public function getPermissions(): array;
}