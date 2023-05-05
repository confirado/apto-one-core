<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Backend\Query\FrontendUser\FrontendUserFinder;
use Apto\Base\Domain\Core\Model\FrontendUser\FrontendUser;

class FrontendUserOrmFinder extends AptoOrmFinder implements FrontendUserFinder
{
    const ENTITY_CLASS = FrontendUser::class;

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
            'externalCustomerGroupId',
            'active',
            'created'
        ];
        if (!$secure) {
            $values[] = 'password';
        }

        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty($property, $value)
            ->setValues([
                'f' => $values
            ])
            ->setPostProcess([
                'f' => [
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
                'f' => [
                    ['id.id', 'id'],
                    ['username.username', 'username'],
                    'email',
                    'externalCustomerGroupId',
                    'active',
                    'created'
                ],
            ])
            ->setPostProcess([
                'f' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setSearch([
                'f' => [
                    'id.id',
                    'username.username',
                    'email'
                ]
            ], $searchString)
            ->setOrderBy([
                ['f.created', 'ASC']
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
                $userWhere .= (false === $hasUserIds ? '(' : ' OR ') . 'f.id.id = :u_user_id_' . $userCount;
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
            'externalCustomerGroupId',
            'active',
            'created'
        ];
        if (!$secure) {
            $values[] = 'password';
        }

        $builder
            ->setValues([
                'f' => $values
            ])
            ->setWhere($userWhere, $userIdParams)
            ->setPostProcess([
                'f' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getResult($this->entityManager);
    }
}
