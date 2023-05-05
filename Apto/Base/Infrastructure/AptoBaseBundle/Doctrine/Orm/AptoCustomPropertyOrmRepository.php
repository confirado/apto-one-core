<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\AptoCustomProperty;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyRepository;

class AptoCustomPropertyOrmRepository extends AptoOrmRepository implements AptoCustomPropertyRepository
{
    const ENTITY_CLASS = AptoCustomProperty::class;

    /**
     * @param AptoCustomProperty $model
     */
    public function update(AptoCustomProperty $model)
    {
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
     * @param int $surrogateId
     * @return AptoCustomProperty|null
     */
    public function findById($surrogateId)
    {
        $builder = $this->createQueryBuilder('AptoCustomProperty')
            ->where('AptoCustomProperty.surrogate_id = :surrogateId')
            ->setParameter('surrogateId', $surrogateId);

        return $builder->getQuery()->getOneOrNullResult();
    }
}