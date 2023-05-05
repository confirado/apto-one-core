<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfigurationRepository;

class BasketConfigurationOrmRepository extends AptoOrmRepository implements BasketConfigurationRepository
{
    const ENTITY_CLASS = BasketConfiguration::class;

    /**
     * @param BasketConfiguration $model
     */
    public function update(BasketConfiguration $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param BasketConfiguration $model
     */
    public function add(BasketConfiguration $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param BasketConfiguration $model
     */
    public function remove(BasketConfiguration $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return BasketConfiguration|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('BasketConfiguration')
            ->where('BasketConfiguration.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}