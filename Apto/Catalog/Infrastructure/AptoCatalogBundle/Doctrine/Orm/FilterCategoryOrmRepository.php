<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Filter\FilterCategory;
use Apto\Catalog\Domain\Core\Model\Filter\FilterCategoryRepository;

class FilterCategoryOrmRepository extends AptoOrmRepository implements FilterCategoryRepository
{
    const ENTITY_CLASS = FilterCategory::class;

    /**
     * @param FilterCategory $model
     */
    public function update(FilterCategory $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param FilterCategory $model
     */
    public function add(FilterCategory $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param FilterCategory $model
     */
    public function remove(FilterCategory $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return FilterCategory|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('FilterCategory')
            ->where('FilterCategory.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $identifier
     * @return FilterCategory|null
     */
    public function findByIdentifier(string $identifier)
    {
        $builder = $this->createQueryBuilder('FilterCategory')
            ->where('FilterCategory.identifier.value = :identifier')
            ->setParameter('identifier', $identifier);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
