<?php

namespace Apto\Plugins\FloatInputElement\Infrastructure\FloatInputElementBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem\FloatInputItem;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem\FloatInputItemRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

class FloatInputItemOrmRepository extends AptoOrmRepository implements FloatInputItemRepository
{
    const ENTITY_CLASS = FloatInputItem::class;

    /**
     * @param FloatInputItem $model
     * @throws ORMException
     */
    public function update(FloatInputItem $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param FloatInputItem $model
     * @throws ORMException
     */
    public function add(FloatInputItem $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param FloatInputItem $model
     * @throws ORMException
     */
    public function remove(FloatInputItem $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return FloatInputItem|null
     * @throws NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('FloatInputItem')
            ->where('FloatInputItem.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return array
     */
    public function findByProductId($id)
    {
        $builder = $this->createQueryBuilder('FloatInputItem')
            ->where('FloatInputItem.productId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return array
     */
    public function findBySectionId($id)
    {
        $builder = $this->createQueryBuilder('FloatInputItem')
            ->where('FloatInputItem.sectionId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return FloatInputItem|null
     * @throws NonUniqueResultException
     */
    public function findByElementId($id)
    {
        $builder = $this->createQueryBuilder('FloatInputItem')
            ->where('FloatInputItem.elementId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}