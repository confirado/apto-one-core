<?php
namespace Apto\Base\Domain\Backend\Model\UserLicence;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface UserLicenceRepository extends AptoRepository
{
    /**
     * @param UserLicence $model
     */
    public function update(UserLicence $model);

    /**
     * @param UserLicence $model
     */
    public function add(UserLicence $model);

    /**
     * @param UserLicence $model
     */
    public function remove(UserLicence $model);

    /**
     * @param string $id
     * @return UserLicence|null
     */
    public function findById($id);

    /**
     * @return UserLicence|null
     */
    public function findCurrent();
}