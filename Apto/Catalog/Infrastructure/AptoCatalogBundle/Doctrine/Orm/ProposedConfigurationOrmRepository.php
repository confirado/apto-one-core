<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\ProposedConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\ProposedConfigurationRepository;

class ProposedConfigurationOrmRepository extends AptoOrmRepository implements ProposedConfigurationRepository
{
    const ENTITY_CLASS = ProposedConfiguration::class;

    /**
     * @param ProposedConfiguration $model
     */
    public function update(ProposedConfiguration $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param ProposedConfiguration $model
     */
    public function add(ProposedConfiguration $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param ProposedConfiguration $model
     */
    public function remove(ProposedConfiguration $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return ProposedConfiguration|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('ProposedConfiguration')
            ->where('ProposedConfiguration.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
