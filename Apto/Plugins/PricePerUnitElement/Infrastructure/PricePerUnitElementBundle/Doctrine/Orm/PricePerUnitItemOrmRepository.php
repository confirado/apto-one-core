<?php

namespace Apto\Plugins\PricePerUnitElement\Infrastructure\PricePerUnitElementBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem\PricePerUnitItem;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem\PricePerUnitItemRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

class PricePerUnitItemOrmRepository extends AptoOrmRepository implements PricePerUnitItemRepository
{
    const ENTITY_CLASS = PricePerUnitItem::class;

    /**
     * @param PricePerUnitItem $model
     * @throws ORMException
     */
    public function update(PricePerUnitItem $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param PricePerUnitItem $model
     * @throws ORMException
     */
    public function add(PricePerUnitItem $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param PricePerUnitItem $model
     * @throws ORMException
     */
    public function remove(PricePerUnitItem $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return PricePerUnitItem|null
     * @throws NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('PricePerUnitItem')
            ->where('PricePerUnitItem.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return array
     */
    public function findByProductId($id)
    {
        $builder = $this->createQueryBuilder('PricePerUnitItem')
            ->where('PricePerUnitItem.productId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return array
     */
    public function findBySectionId($id)
    {
        $builder = $this->createQueryBuilder('PricePerUnitItem')
            ->where('PricePerUnitItem.sectionId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return PricePerUnitItem|null
     * @throws NonUniqueResultException
     */
    public function findByElementId($id)
    {
        $builder = $this->createQueryBuilder('PricePerUnitItem')
            ->where('PricePerUnitItem.elementId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}