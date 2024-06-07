<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\AptoCustomProperty;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyRepository;
use Doctrine\ORM\NonUniqueResultException as NonUniqueResultExceptionAlias;

class AptoCustomPropertyOrmRepository extends AptoOrmRepository implements AptoCustomPropertyRepository
{
    const ENTITY_CLASS = AptoCustomProperty::class;

    /**
     * @param AptoCustomProperty $model
     */
    public function update(AptoCustomProperty $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param AptoCustomProperty $model
     */
    public function add(AptoCustomProperty $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param AptoCustomProperty $model
     */
    public function remove(AptoCustomProperty $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param $surrogateId
     * @return AptoCustomProperty|null
     * @throws NonUniqueResultExceptionAlias
     */
    public function findBySurrogateId($surrogateId): ?AptoCustomProperty
    {
        $builder = $this->createQueryBuilder('AptoCustomProperty')
            ->where('AptoCustomProperty.surrogateId = :surrogateId')
            ->setParameter('surrogateId', $surrogateId);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return AptoCustomProperty|null
     * @throws NonUniqueResultExceptionAlias
     */
    public function findById($id): ?AptoCustomProperty
    {

        $builder = $this->createQueryBuilder('AptoCustomProperty')
            ->where('AptoCustomProperty.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
