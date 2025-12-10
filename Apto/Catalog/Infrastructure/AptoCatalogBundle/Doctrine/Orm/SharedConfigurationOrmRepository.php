<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\SharedConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\SharedConfigurationRepository;

class SharedConfigurationOrmRepository extends AptoOrmRepository implements SharedConfigurationRepository
{
    const ENTITY_CLASS = SharedConfiguration::class;

    /**
     * @param SharedConfiguration $model
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(SharedConfiguration $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param SharedConfiguration $model
     */
    public function add(SharedConfiguration $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param SharedConfiguration $model
     */
    public function remove(SharedConfiguration $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param mixed $id
     * @return SharedConfiguration|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('SharedConfiguration')
            ->where('SharedConfiguration.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
