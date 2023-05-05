<?php

namespace Apto\Plugins\RequestForm\Infrastructure\RequestFormBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\RequestForm\Domain\Core\Model\OfferData\OfferData;
use Apto\Plugins\RequestForm\Domain\Core\Model\OfferData\OfferDataRepository;

class OfferDataOrmRepository extends AptoOrmRepository implements OfferDataRepository
{
    const ENTITY_CLASS = OfferData::class;

    /**
     * @param OfferData $model
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(OfferData $model)
    {
        $this->_em->persist($model);
    }
}