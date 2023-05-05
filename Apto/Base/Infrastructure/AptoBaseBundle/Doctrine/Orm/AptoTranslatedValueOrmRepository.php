<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoTranslatedValueRepository;

class AptoTranslatedValueOrmRepository extends AptoOrmRepository implements AptoTranslatedValueRepository
{
    /**
     * @param AptoTranslatedValue $model
     */
    public function update(AptoTranslatedValue $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param AptoTranslatedValue $model
     */
    public function add(AptoTranslatedValue $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param AptoTranslatedValue $model
     */
    public function remove(AptoTranslatedValue $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return AptoTranslatedValue|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('AptoTranslatedValue')
            ->where('AptoTranslatedValue.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}