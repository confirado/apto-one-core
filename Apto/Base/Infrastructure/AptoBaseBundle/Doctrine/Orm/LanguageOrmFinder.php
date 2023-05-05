<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Query\Language\LanguageFinder;
use Apto\Base\Domain\Core\Model\Language\Language;

class LanguageOrmFinder extends AptoOrmFinder implements LanguageFinder
{
    const ENTITY_CLASS = Language::class;

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'l' => [
                    ['id.id', 'id'],
                    'name',
                    ['isocode.name', 'isocode'],
                    'created'
                ]
            ])
            ->setPostProcess([
                'l' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLanguages(string $searchString = ''): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setValues([
                'l' => [
                    ['id.id', 'id'],
                    'name',
                    ['isocode.name', 'isocode'],
                    'created'
                ]
            ])
            ->setPostProcess([
                'l' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ])
            ->setSearch([
                'l' => [
                    'id.id',
                    'name',
                    'isocode.name'
                ]
            ], $searchString)
            ->setOrderBy([
                ['l.created', 'ASC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @return array
     * @throws DqlBuilderException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findTranslatedValues(): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setValues([
                'l' => [
                    ['id.id', 'id'],
                    ['isocode.name', 'isocode'],
                    'name'
                ]
            ])
            ->setPostProcess([
                'l' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        return $builder->getResult($this->entityManager);
    }
}
