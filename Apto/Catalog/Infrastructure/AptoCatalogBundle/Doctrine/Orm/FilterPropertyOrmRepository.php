<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Filter\FilterProperty;
use Apto\Catalog\Domain\Core\Model\Filter\FilterPropertyRepository;

class FilterPropertyOrmRepository extends AptoOrmRepository implements FilterPropertyRepository
{
    const ENTITY_CLASS = FilterProperty::class;

    /**
     * @param FilterProperty $model
     */
    public function update(FilterProperty $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param FilterProperty $model
     */
    public function add(FilterProperty $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param FilterProperty $model
     */
    public function remove(FilterProperty $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return FilterProperty|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('FilterProperty')
            ->where('FilterProperty.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $identifier
     * @return FilterProperty|null
     */
    public function findByIdentifier(string $identifier)
    {
        $builder = $this->createQueryBuilder('FilterProperty')
            ->where('FilterProperty.identifier.value = :identifier')
            ->setParameter('identifier', $identifier);

        return $builder->getQuery()->getOneOrNullResult();
    }
}