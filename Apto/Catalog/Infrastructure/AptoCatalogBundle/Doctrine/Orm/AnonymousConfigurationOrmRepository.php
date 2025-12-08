<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\AnonymousConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\AnonymousConfigurationRepository;

class AnonymousConfigurationOrmRepository extends AptoOrmRepository implements AnonymousConfigurationRepository
{
    const ENTITY_CLASS = AnonymousConfiguration::class;

    /**
     * @param AnonymousConfiguration $model
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(AnonymousConfiguration $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param AnonymousConfiguration $model
     */
    public function add(AnonymousConfiguration $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param AnonymousConfiguration $model
     */
    public function remove(AnonymousConfiguration $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param mixed $id
     * @return AnonymousConfiguration|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('AnonymousConfiguration')
            ->where('AnonymousConfiguration.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
