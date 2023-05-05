<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\Material;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\MaterialRepository;

class MaterialOrmRepository extends AptoOrmRepository implements MaterialRepository
{
    const ENTITY_CLASS = Material::class;

    /**
     * @param Material $model
     */
    public function update(Material $model)
    {
        $this->_em->merge($model);
        $this->invalidateCache();
    }

    /**
     * @param Material $model
     */
    public function add(Material $model)
    {
        $this->_em->persist($model);
        $this->invalidateCache();
    }

    /**
     * @param Material $model
     */
    public function remove(Material $model)
    {
        $this->_em->remove($model);
        $this->invalidateCache();
    }

    /**
     * @param string $id
     * @return Material|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('Material')
            ->where('Material.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $identifier
     * @return Material|null
     */
    public function findFirstIdByIdentifier($identifier)
    {
        $builder = $this
            ->createQueryBuilder('Material')
            ->select('Material.id.id as id, Material.identifier')
            ->where('Material.identifier = :identifier')
            ->setParameter('identifier', $identifier);

        $result = $builder->getQuery()->getScalarResult();

        if (count($result) > 0) {
            return $result[0]['id'];
        }

        return null;
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