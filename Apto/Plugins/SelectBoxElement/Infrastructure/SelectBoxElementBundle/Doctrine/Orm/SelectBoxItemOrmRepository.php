<?php

namespace Apto\Plugins\SelectBoxElement\Infrastructure\SelectBoxElementBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItem;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItemRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

class SelectBoxItemOrmRepository extends AptoOrmRepository implements SelectBoxItemRepository
{
    const ENTITY_CLASS = SelectBoxItem::class;

    /**
     * @param SelectBoxItem $model
     * @throws ORMException
     */
    public function update(SelectBoxItem $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param SelectBoxItem $model
     * @throws ORMException
     */
    public function add(SelectBoxItem $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param SelectBoxItem $model
     * @throws ORMException
     */
    public function remove(SelectBoxItem $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param $id
     * @return SelectBoxItem|int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('SelectBoxItem')
            ->where('SelectBoxItem.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return array
     */
    public function findByProductId($id)
    {
        $builder = $this->createQueryBuilder('SelectBoxItem')
            ->where('SelectBoxItem.productId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return array
     */
    public function findBySectionId($id)
    {
        $builder = $this->createQueryBuilder('SelectBoxItem')
            ->where('SelectBoxItem.sectionId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return array
     */
    public function findByElementId($id)
    {
        $builder = $this->createQueryBuilder('SelectBoxItem')
            ->where('SelectBoxItem.elementId = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getResult();
    }
}
