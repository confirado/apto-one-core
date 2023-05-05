<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Query\AptoFinder;
use Doctrine\ORM\EntityManagerInterface;

abstract class AptoOrmFinder implements AptoFinder
{
    const ENTITY_CLASS = 'SET_ENTITY_CLASS_IN_CHILD_CLASS';

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * AptoOrmFinder constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityClass = static::ENTITY_CLASS;
    }
}