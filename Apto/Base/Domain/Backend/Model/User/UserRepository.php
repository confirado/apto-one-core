<?php
namespace Apto\Base\Domain\Backend\Model\User;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface UserRepository extends AptoRepository
{
    /**
     * @param User $model
     */
    public function update(User $model);

    /**
     * @param User $model
     */
    public function add(User $model);

    /**
     * @param User $model
     */
    public function remove(User $model);

    /**
     * @param string $id
     * @return User|null
     */
    public function findById($id);

    /**
     * @param string $username
     * @return User|null
     */
    public function findOneByUsername($username);

    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail($email);

    /**
     * @param string $apiKey
     * @return User|null
     */
    public function findOneByApiKey($apiKey);
}