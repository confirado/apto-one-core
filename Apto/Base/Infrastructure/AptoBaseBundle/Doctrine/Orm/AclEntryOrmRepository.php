<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Backend\Model\Acl\AclEntry;
use Apto\Base\Domain\Backend\Model\Acl\AclEntryRepository;
use Apto\Base\Domain\Backend\Model\Acl\AclIdentity;

class AclEntryOrmRepository extends AptoOrmRepository implements AclEntryRepository
{
    const ENTITY_CLASS = AclEntry::class;

    /**
     * @param AclEntry $model
     */
    public function update(AclEntry $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param AclEntry $model
     */
    public function add(AclEntry $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param AclEntry $model
     */
    public function remove(AclEntry $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $shopId
     */
    public function removeByShopId(string $shopId)
    {
        $builder = $this->createQueryBuilder('AclEntry');
        $builder
            ->delete()
            ->where('AclEntry.shop = :shopId')
            ->setParameter('shopId', $shopId)
            ->getQuery()->execute();
    }

    /**
     * @param string $id
     * @return AclEntry|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('AclEntry')
            ->where('AclEntry.surrogateId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string|null $shop
     * @param string $role
     * @param AclIdentity $identity
     * @return AclEntry|null
     */
    public function findByShopRoleIdentity($shop, string $role, AclIdentity $identity)
    {
        $builder = $this->createQueryBuilder('AclEntry');

        $exprShop = $builder->expr()->orX(
            $builder->expr()->eq('AclEntry.shop', ':shop'),
            $builder->expr()->isNull('AclEntry.shop')
        );

        $exprIdentityRole = $builder->expr()->eq('AclEntry.role.identifier', ':role');

        $exprIdentityModelClass = $builder->expr()->eq('AclEntry.identity.modelClass', ':model');

        $exprIdentityId = $builder->expr()->orX(
            $builder->expr()->eq('AclEntry.identity.entityId', ':entity'),
            $builder->expr()->isNull('AclEntry.identity.entityId')
        );

        $exprIdentityFieldName = $builder->expr()->orX(
            $builder->expr()->eq('AclEntry.identity.fieldName', ':field'),
            $builder->expr()->isNull('AclEntry.identity.fieldName')
        );

        $expr = $builder->expr()->andX(
            $exprShop,
            $exprIdentityRole,
            $exprIdentityModelClass,
            $exprIdentityId,
            $exprIdentityFieldName
        );

        $builder->where($expr);
        $builder->setParameter('shop', $shop)
            ->setParameter('role', $role)
            ->setParameter('model', $identity->getModelClass())
            ->setParameter('entity', $identity->getEntityId())
            ->setParameter('field', $identity->getFieldName());

        return $builder->getQuery()->getOneOrNullResult();
    }
}
