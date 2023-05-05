<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Backend\Model\User\User;
use Apto\Base\Application\Backend\Query\User\UserFinder;

class UserOrmFinder extends AptoOrmFinder implements UserFinder
{
    const ENTITY_CLASS = User::class;
    
    /**
     * @param string $property
     * @param string $value
     * @param bool $secure
     * @return array|null
     * @throws DqlBuilderException
     */
    protected function findByProperty(string $property, string $value, bool $secure)
    {
        $values = [
            ['id.id', 'id'],
            ['username.username', 'username'],
            'email',
            'active',
            'rte',
            'apiKey',
            'apiOrigin',
            'userLicenceHash',
            'userLicenceSignatureTimestamp',
            'created'
        ];
        if (!$secure) {
            $values[] = 'password';
        }

        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty($property, $value)
            ->setValues([
                'u' => $values,
                'r' => [
                    ['id.id', 'id'],
                    ['identifier.identifier', 'identifier'],
                    'name'
                ]
            ])
            ->setJoins([
                'u' => [
                    ['userRoles', 'r', 'id']
                ]
            ])
            ->setPostProcess([
                'u' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @param bool $secure
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findById(string $id, bool $secure = true)
    {
        return $this->findByProperty('id.id', $id, $secure);
    }

    /**
     * @param string $username
     * @param bool $secure
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByUsername(string $username, bool $secure = true)
    {
        return $this->findByProperty('username.username', $username, $secure);
    }

    /**
     * @param string $apiKey
     * @param bool $secure
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByApiKey(string $apiKey, bool $secure = true)
    {
        return $this->findByProperty('apiKey', $apiKey, $secure);
    }

    /**
     * @param string $apiOrigin
     * @param bool $secure
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByApiOrigin(string $apiOrigin, bool $secure = true)
    {
        return $this->findByProperty('apiOrigin', $apiOrigin, $secure);
    }

    /**
     * @param string $email
     * @param bool $secure
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findByEmail(string $email, bool $secure = true)
    {
        return $this->findByProperty('email', $email, $secure);
    }

    /**
     * @param string $searchString
     * @param bool $secure
     * @return array
     * @throws DqlBuilderException
     */
    public function findUsers(string $searchString = '', bool $secure = true): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'u' => [
                    ['id.id', 'id'],
                    ['username.username', 'username'],
                    'email',
                    'active',
                    'rte',
                    'created'
                ],
                'r' => [
                    ['id.id', 'id'],
                    ['identifier.identifier', 'identifier'],
                    'name'
                ]
            ])
            ->setPostProcess([
                'u' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setSearch([
                'u' => [
                    'id.id',
                    'username.username',
                    'email'
                ]
            ], $searchString)
            ->setJoins([
                'u' => [
                    ['userRoles', 'r', 'id']
                ]
            ])
            ->setOrderBy([
                ['u.created', 'ASC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param array $userIds
     * @param bool $secure
     * @return array
     * @throws DqlBuilderException
     */
    public function findFindUsersByUserIds(array $userIds = [], bool $secure = true): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);

        $userWhere = '';
        $userIdParams = [];
        $hasUserIds = false;

        $userCount = 0;
        foreach ($userIds as $user) {
            if ($user['userId']) {
                $userWhere .= (false === $hasUserIds ? '(' : ' OR ') . 'u.id.id = :u_user_id_' . $userCount;
                $userIdParams['u_user_id_' . $userCount] = $user['userId'];
                $hasUserIds = true;
                $userCount++;
            }
        }
        if (true === $hasUserIds) {
            $userWhere .= ')';
        }

        if ('' == $userWhere) {
            return $builder->getEmptyResult();
        }

        $values = [
            ['id.id', 'id'],
            ['username.username', 'username'],
            'email',
            'active',
            'rte',
            'created'
        ];
        if (!$secure) {
            $values[] = 'password';
            $values[] = 'apiKey';
            $values[] = 'apiOrigin';
        }

        $builder
            ->setValues([
                'u' => $values
            ])
            ->setWhere($userWhere, $userIdParams)
            ->setPostProcess([
                'u' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getResult($this->entityManager);
    }
}