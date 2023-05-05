<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Dql;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractDqlService
{
    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param string $entityClass
     */
    public function __construct(EntityManagerInterface $entityManager, string $entityClass = '')
    {
        $this->entityManager = $entityManager;
        $this->entityClass = $entityClass;
    }
}