<?php

namespace Apto\Plugins\PdfGenerator\Infrastructure\PdfGeneratorBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\RandomNumber\RandomNumber;
use Apto\Plugins\PdfGenerator\Domain\Core\Model\RandomNumber\RandomNumberRepository;

class RandomNumberOrmRepository extends AptoOrmRepository implements RandomNumberRepository
{

    const ENTITY_CLASS = RandomNumber::class;

    /**
     * @param RandomNumber $model
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(RandomNumber $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param $number
     * @return RandomNumber
     */
    public function findByNumber($number): ?RandomNumber
    {
        $dql = 'SELECT r.number FROM ' . RandomNumber::class . ' r WHERE r.number = :number';

        $result = $this->_em
            ->createQuery($dql)
            ->setParameters([
                'number' => $number
            ])
            ->getResult();

        return count($result) > 0 ? $result : null;
    }
}
