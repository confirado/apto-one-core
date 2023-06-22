<?php

namespace Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlPaginatorBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Plugins\ImageUpload\Application\Core\Query\Canvas\CanvasFinder;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas\Canvas;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class CanvasOrmFinder extends AptoOrmFinder implements CanvasFinder
{
    const ENTITY_CLASS = Canvas::class;

    /**
     * @param AptoUuid $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findById(AptoUuid $id): ?array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id->getId())
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'imageSettings',
                    'motiveSettings',
                    'textSettings',
                    'areaSettings',
                    'priceSettings',
                    'created'
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'imageSettings' => [self::class, 'decodeSerialized'],
                    'motiveSettings' => [self::class, 'decodeSerialized'],
                    'textSettings' => [self::class, 'decodeSerialized'],
                    'areaSettings' => [self::class, 'decodeSerialized'],
                    'priceSettings' => [self::class, 'decodeSerialized']
                ]
            ])
        ;

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findList(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setPage($pageNumber)
            ->setRecordsPerPage($recordsPerPage)
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'imageSettings',
                    'motiveSettings',
                    'textSettings',
                    'areaSettings',
                    'priceSettings',
                    'created'
                ]
            ])
            ->setSearch([
                'c' => [
                    'id.id',
                    'identifier.value'
                ]
            ], $searchString)
            ->setPostProcess([
                'c' => [
                    'imageSettings' => [self::class, 'decodeSerialized'],
                    'motiveSettings' => [self::class, 'decodeSerialized'],
                    'textSettings' => [self::class, 'decodeSerialized'],
                    'areaSettings' => [self::class, 'decodeSerialized'],
                    'priceSettings' => [self::class, 'decodeSerialized']
                ]
            ])
            ->setOrderBy([
                ['c.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @return array
     * @throws DqlBuilderException
     */
    public function findIds(): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'c' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ]
            ])
            ->setOrderBy([
                ['c.identifier.value', 'ASC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function decodeSerialized($value)
    {
        return unserialize(trim($value));
    }
}
