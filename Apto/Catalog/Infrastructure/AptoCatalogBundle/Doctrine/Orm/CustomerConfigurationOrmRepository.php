<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\CustomerConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\CustomerConfigurationRepository;

class CustomerConfigurationOrmRepository extends AptoOrmRepository implements CustomerConfigurationRepository
{
    const ENTITY_CLASS = CustomerConfiguration::class;

    /**
     * @param CustomerConfiguration $model
     */
    public function update(CustomerConfiguration $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param CustomerConfiguration $model
     */
    public function add(CustomerConfiguration $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param CustomerConfiguration $model
     */
    public function remove(CustomerConfiguration $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return CustomerConfiguration|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('CustomerConfiguration')
            ->where('CustomerConfiguration.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
