<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\GuestConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\GuestConfigurationRepository;

class GuestConfigurationOrmRepository extends AptoOrmRepository implements GuestConfigurationRepository
{
    const ENTITY_CLASS = GuestConfiguration::class;

    /**
     * @param GuestConfiguration $model
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(GuestConfiguration $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param GuestConfiguration $model
     */
    public function add(GuestConfiguration $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param GuestConfiguration $model
     */
    public function remove(GuestConfiguration $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param mixed $id
     * @return GuestConfiguration|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('GuestConfiguration')
            ->where('GuestConfiguration.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
