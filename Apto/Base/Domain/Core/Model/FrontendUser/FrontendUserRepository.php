<?php

namespace Apto\Base\Domain\Core\Model\FrontendUser;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface FrontendUserRepository extends AptoRepository
{
    /**
     * @param FrontendUser $model
     */
    public function update(FrontendUser $model);

    /**
     * @param FrontendUser $model
     */
    public function add(FrontendUser $model);

    /**
     * @param FrontendUser $model
     */
    public function remove(FrontendUser $model);

    /**
     * @param string $id
     * @return FrontendUser|null
     */
    public function findById($id);

    /**
     * @param string $username
     * @return FrontendUser|null
     */
    public function findOneByUsername($username);

    /**
     * @param string $email
     * @return FrontendUser|null
     */
    public function findOneByEmail($email);
}
