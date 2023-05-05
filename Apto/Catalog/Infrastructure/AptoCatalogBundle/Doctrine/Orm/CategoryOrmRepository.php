<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Category\Category;
use Apto\Catalog\Domain\Core\Model\Category\CategoryRepository;

class CategoryOrmRepository extends AptoOrmRepository implements CategoryRepository
{
    const ENTITY_CLASS = Category::class;

    /**
     * @param Category $model
     */
    public function update(Category $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param Category $model
     */
    public function add(Category $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param Category $model
     */
    public function remove(Category $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return Category|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('Category')
            ->where('Category.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $name
     * @return Category|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByName(string $name)
    {
        $builder = $this->createQueryBuilder('Category')
            ->where("JSON_SEARCH(Category.name, 'all', :name) IS NOT NULL")
            ->setParameter('name', $name);
        return $builder->getQuery()->getOneOrNullResult();
    }
}