<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Backend\Model\UserLicence\UserLicence;
use Apto\Base\Application\Backend\Query\UserLicence\UserLicenceFinder;

class UserLicenceOrmFinder extends AptoOrmFinder implements UserLicenceFinder
{
    const ENTITY_CLASS = UserLicence::class;
    
    /**
     * @param string $id
     * @return array
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass, 'ul');
        $builder
            ->findById($id)
            ->setValues([
                'ul' => [
                    ['id.id', 'id'],
                    'title',
                    'text',
                    'validSince'
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @return array
     */
    public function findCurrent()
    {
        $builder = new DqlQueryBuilder($this->entityClass, 'ul');
        $builder
            ->setWhere('ul.validSince <= :now', [
                'now' => new \DateTimeImmutable()
            ])
            ->setValues([
                'ul' => [
                    ['id.id', 'id'],
                    'title',
                    'text',
                    'validSince'
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }
}