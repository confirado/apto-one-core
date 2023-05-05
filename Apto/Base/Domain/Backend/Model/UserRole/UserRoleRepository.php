<?php

namespace Apto\Base\Domain\Backend\Model\UserRole;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface UserRoleRepository extends AptoRepository
{
    /**
     * @param UserRole $model
     */
    public function update(UserRole $model);

    /**
     * @param UserRole $model
     */
    public function add(UserRole $model);

    /**
     * @param UserRole $model
     */
    public function remove(UserRole $model);

    /**
     * @param string $id
     * @return UserRole|null
     */
    public function findById($id);

    /**
     * @param string $identifier
     * @return UserRole|null
     */
    public function findOneByIdentifier($identifier);
}