<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\Property;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\PropertyRepository;

class PropertyOrmRepository extends AptoOrmRepository implements PropertyRepository
{
    const ENTITY_CLASS = Property::class;

    /**
     * @param Property $model
     */
    public function update(Property $model)
    {
        $this->_em->merge($model);
        $this->invalidateCache();
    }

    /**
     * @param Property $model
     */
    public function add(Property $model)
    {
        $this->_em->persist($model);
        $this->invalidateCache();
    }

    /**
     * @param Property $model
     */
    public function remove(Property $model)
    {
        $this->_em->remove($model);
        $this->invalidateCache();
    }

    /**
     * @param string $id
     * @return Property|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('Property')
            ->where('Property.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return void
     */
    public function invalidateCache()
    {
        AptoCacheService::clearCache('PoolItemsFiltered-');
        AptoCacheService::clearCache('PoolColorItemsFiltered-');
    }
}