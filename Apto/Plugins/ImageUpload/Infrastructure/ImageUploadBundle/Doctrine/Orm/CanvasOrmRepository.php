<?php

namespace Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Doctrine\ORM\NonUniqueResultException;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas\Canvas;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas\CanvasRepository;

class CanvasOrmRepository extends AptoOrmRepository implements CanvasRepository
{
    const ENTITY_CLASS = Canvas::class;

    /**
     * @param Canvas $model
     * @return void
     */
    public function update(Canvas $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param Canvas $model
     * @return void
     */
    public function add(Canvas $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param Canvas $model
     * @return void
     */
    public function remove(Canvas $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param AptoUuid $id
     * @return Canvas|null
     * @throws NonUniqueResultException
     */
    public function findById(AptoUuid $id): ?Canvas
    {
        $builder = $this->createQueryBuilder('Canvas')
            ->where('Canvas.id.id = :id')
            ->setParameter('id', $id->getId());

        $result = $builder->getQuery()->getOneOrNullResult();

        if ($result instanceof Canvas) {
            return $result;
        }

        return null;
    }

    /**
     * @param Identifier $identifier
     * @return Canvas|null
     * @throws NonUniqueResultException
     */
    public function findByIdentifier(Identifier $identifier): ?Canvas
    {
        $builder = $this->createQueryBuilder('Canvas')
            ->where('Canvas.identifier.value = :identifier')
            ->setParameter('identifier', $identifier->getValue());

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Identifier $identifier
     * @return AptoUuid|null
     * @throws InvalidUuidException
     * @throws NonUniqueResultException
     */
    public function findIdByIdentifier(Identifier $identifier): ?AptoUuid
    {
        // create query
        $builder = $this
            ->createQueryBuilder('Canvas')
            ->select('Canvas.id.id as id, Canvas.identifier.value as identifier')
            ->where('Canvas.identifier.value = :identifier')
            ->setParameter('identifier', $identifier->getValue())
        ;

        $result = $builder->getQuery()->getOneOrNullResult();

        if (null === $result) {
            return $result;
        }

        // return null if no name matched
        return new AptoUuid($result['id']);
    }
}
