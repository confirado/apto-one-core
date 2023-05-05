<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Apto\Base\Domain\Core\Model\AptoRepository;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AptoRepository
 * @package Apto\Base\Infrastructure\AptoBaseBundle\Domain\Core\Model
 */
abstract class AptoOrmRepository extends ServiceEntityRepository implements AptoRepository
{
    const ENTITY_CLASS = 'SET_ENTITY_CLASS_IN_CHILD_CLASS';

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, static::ENTITY_CLASS);
    }

    /**
     * @return AptoUuid
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     */
    public function nextIdentity(): AptoUuid
    {
        return new AptoUuid();
    }
}