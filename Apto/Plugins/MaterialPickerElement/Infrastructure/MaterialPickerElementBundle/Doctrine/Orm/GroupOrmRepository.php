<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\Group;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\GroupRepository;

class GroupOrmRepository extends AptoOrmRepository implements GroupRepository
{
    const ENTITY_CLASS = Group::class;

    /**
     * @param Group $model
     */
    public function update(Group $model)
    {
        $this->_em->merge($model);
        $this->invalidateCache();
    }

    /**
     * @param Group $model
     */
    public function add(Group $model)
    {
        $this->_em->persist($model);
        $this->invalidateCache();
    }

    /**
     * @param Group $model
     */
    public function remove(Group $model)
    {
        $this->_em->remove($model);
        $this->invalidateCache();
    }

    /**
     * @param string $id
     * @return Group|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('PropertyGroup')
            ->where('PropertyGroup.id.id = :id')
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