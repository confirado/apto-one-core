<?php

namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\Doctrine\Orm\Part;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Dql\ProductDqlService;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\PartRepository;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class PartOrmRepository extends AptoOrmRepository implements PartRepository
{
    const ENTITY_CLASS = Part::class;

    /**
     * @param Part $model
     * @throws ORMException
     */
    public function add(Part $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param Part $model
     * @throws ORMException
     */
    public function update(Part $model)
    {
        $this->_em->merge($model);
    }

    /**
     * @param Part $model
     * @throws ORMException
     */
    public function remove(Part $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param Part $model
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush(Part $model)
    {
        $this->_em->flush($model);
    }

    /**
     * @param string $id
     * @return Part|mixed|null
     * @throws NonUniqueResultException
     */
    public function findById(string $id)
    {
        $builder = $this->createQueryBuilder('Part')
            ->where('Part.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $partNumber
     * @return Part|null
     * @throws NonUniqueResultException
     */
    public function findByPartNumber(string $partNumber)
    {
        $builder = $this->createQueryBuilder('Part')
            ->where('Part.partNumber = :partNumber')
            ->setParameter('partNumber', $partNumber);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $ids
     * @return array
     * @throws NonUniqueResultException
     */
    public function findPartCustomProperties(array $ids): array
    {
        $customProperties = [];

        foreach ($ids as $id) {
            $part = $this->findById($id);
            if ($part) {
                $customProperties[$id] = $part->getCustomProperties();
            }
        }

        return $customProperties;
    }

    /**
     * @param array $productUsageIds
     * @param array $sectionUsageIds
     * @param array $elementUsageIds
     * @return Collection
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findByUsages(array $productUsageIds, array $sectionUsageIds, array $elementUsageIds): Collection
    {
        $builderPu = $this->createQueryBuilder('Part')
            ->indexBy('Part', 'Part.id.id')
            ->join('Part.productUsages', 'pu')
            ->where('pu.usageForUuid.id in (:productUsageIds)')
            ->setParameter('productUsageIds', $productUsageIds)
        ;

        $builderSu = $this->createQueryBuilder('Part')
            ->indexBy('Part', 'Part.id.id')
            ->join('Part.sectionUsages', 'su')
            ->where('su.usageForUuid.id in (:sectionUsageIds)')
            ->setParameter('sectionUsageIds', $sectionUsageIds)
        ;

        $builderEu = $this->createQueryBuilder('Part')
            ->indexBy('Part', 'Part.id.id')
            ->join('Part.elementUsages', 'eu')
            ->where('eu.usageForUuid.id in (:elementUsageIds)')
            ->setParameter('elementUsageIds', $elementUsageIds)
        ;

        $builderRu = $this->createQueryBuilder('Part')
            ->indexBy('Part', 'Part.id.id')
            ->join('Part.ruleUsages', 'ru')
            ->join('ru.conditions', 'ruc')
            ->where('ruc.productId.id in (:productUsageIds)')
            ->setParameter('productUsageIds', $productUsageIds)
        ;
        //return $builderRu->getQuery()->getResult();
        return new ArrayCollection(
            array_merge(
                $builderPu->getQuery()->getResult(),
                $builderSu->getQuery()->getResult(),
                $builderEu->getQuery()->getResult(),
                $builderRu->getQuery()->getResult()
            )
        );
    }
}
