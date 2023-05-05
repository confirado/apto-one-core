<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Backend\Model\Acl\AclEntry;
use Apto\Base\Application\Backend\Query\AclEntry\AclEntryFinder;

class AclEntryOrmFinder extends AptoOrmFinder implements AclEntryFinder
{
    const ENTITY_CLASS = AclEntry::class;
    
    /**
     * @param string $aclClass
     * @return array|mixed
     * @throws \Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException
     */
    public function findByAclClass(string $aclClass)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder->findByProperty('identity.modelClass', $aclClass);
        $builder->setValues([
            'a' => [
                ['identity.modelClass', 'modelClass'],
                ['role.identifier', 'role'],
                ['mask.mask', 'mask']
            ]
        ]);

        $result = $builder->getResult($this->entityManager);
        $result['aclClass'] = $aclClass;

        return $result;
    }

    /**
     * @param string $shopId
     * @return array
     */
    public function findByShopId(string $shopId)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder->findByProperty('shop', $shopId);
        $builder->setValues([
            'a' => [
                ['identity.modelClass', 'modelClass'],
                ['role.identifier', 'role'],
                ['mask.mask', 'mask']
            ]
        ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $roleIdentifier
     * @param string $modelClass
     * @param string|null $shopId
     * @param mixed|null $entityId
     * @param string|null $fieldName
     * @return array
     */
    public function findByShopRoleIdentity(string $roleIdentifier, string $modelClass, $shopId = null, $entityId = null, $fieldName = null)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder->setValues([
            'a' => [
                ['identity.modelClass', 'modelClass'],
                ['identity.entityId', 'entityId'],
                ['identity.fieldName', 'fieldName'],
                ['mask.mask', 'mask']
            ]
        ])
        ->setWhere(
            '(a.shop = :shopId OR a.shop IS NULL)
            AND a.role.identifier = :roleIdentifier
            AND a.identity.modelClass = :modelClass
            AND (
                a.identity.entityId = :entityId OR a.identity.entityId IS NULL
            )
            AND (
                a.identity.fieldName = :fieldName OR a.identity.fieldName IS NULL
            )', [
                'shopId' => $shopId,
                'roleIdentifier' => $roleIdentifier,
                'modelClass' => $modelClass,
                'entityId' => $entityId,
                'fieldName' => $fieldName
            ]
        );

        return $builder->getResult($this->entityManager);
    }
}