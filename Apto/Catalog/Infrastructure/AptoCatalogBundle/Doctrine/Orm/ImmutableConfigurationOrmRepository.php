<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Domain\Core\Model\Configuration\ImmutableConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\ImmutableConfigurationRepository;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Dql\ImmutableConfigurationDqlService;

class ImmutableConfigurationOrmRepository extends AptoOrmRepository implements ImmutableConfigurationRepository
{
    const ENTITY_CLASS = ImmutableConfiguration::class;

    /**
     * @param ImmutableConfiguration $model
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(ImmutableConfiguration $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param ImmutableConfiguration $model
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(ImmutableConfiguration $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param ImmutableConfiguration $model
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(ImmutableConfiguration $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param $id
     * @return ImmutableConfiguration|mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('ImmutableConfiguration')
            ->where('ImmutableConfiguration.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $conditions
     * @return array
     * @throws DqlBuilderException
     */
    public function findByCustomPropertyValues(array $conditions)
    {
        $dqlService = new ImmutableConfigurationDqlService($this->_em, $this->_entityName);
        return $dqlService->findByCustomPropertyValues($conditions);
    }
}
