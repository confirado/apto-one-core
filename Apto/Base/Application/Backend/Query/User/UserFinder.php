<?php

namespace Apto\Base\Application\Backend\Query\User;

use Apto\Base\Application\Core\Query\AptoFinder;

interface UserFinder extends AptoFinder
{
    /**
     * @param string $id
     * @param bool $secure
     * @return array|null
     */
    public function findById(string $id, bool $secure = true);

    /**
     * @param string $username
     * @param bool $secure
     * @return array|null
     */
    public function findByUsername(string $username, bool $secure = true);

    /**
     * @param string $apiKey
     * @param bool $secure
     * @return array|null
     */
    public function findByApiKey(string $apiKey, bool $secure = true);

    /**
     * @param string $apiOrigin
     * @param bool $secure
     * @return array|null
     */
    public function findByApiOrigin(string $apiOrigin, bool $secure = true);

    /**
     * @param string $email
     * @param bool $secure
     * @return array|null
     */
    public function findByEmail(string $email, bool $secure = true);

    /**
     * @param string $searchString
     * @param bool $secure
     * @return array
     */
    public function findUsers(string $searchString = '', bool $secure = true): array;

    /**
     * @param array $userIds
     * @param bool $secure
     * @return array
     */
    public function findFindUsersByUserIds(array $userIds = [], bool $secure = true): array;
}