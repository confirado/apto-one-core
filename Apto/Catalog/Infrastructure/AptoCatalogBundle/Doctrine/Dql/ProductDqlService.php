<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Dql;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;

class ProductDqlService extends AbstractDqlService
{
    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findSectionsElements(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'ps' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'position',
                    'name',
                    'isActive',
                    'isHidden',
                    'isMandatory',
                    'allowMultiple'
                ],
                'pe' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'isDefault',
                    'isActive',
                    'definition',
                    'position',
                    'name',
                    'errorMessage'
                ],
                'pscp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ],
                'pecp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['sections', 'ps', 'id']
                ],
                'ps' => [
                    ['customProperties', 'pscp', 'surrogateId'],
                    ['elements', 'pe', 'id']
                ],
                'pe' => [
                    ['customProperties', 'pecp', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                'ps' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'isActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isHidden' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isMandatory' => [DqlQueryBuilder::class, 'decodeBool'],
                    'allowMultiple' => [DqlQueryBuilder::class, 'decodeBool'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ],
                'pe' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'isDefault' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'errorMessage' => [DqlQueryBuilder::class, 'decodeJson'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ],
                'pscp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])->setOrderBy([
                ['ps.position', 'ASC'],
                ['ps.identifier.value', 'ASC'],
                ['pe.position', 'ASC'],
                ['pe.identifier.value', 'ASC']
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findTranslatableSectionsElements(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'ps' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'name',
                    'description'
                ],
                'pe' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'definition',
                    'name',
                    'description',
                    'errorMessage'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['sections', 'ps', 'id']
                ],
                'ps' => [
                    ['elements', 'pe', 'id']
                ]
            ])
            ->setPostProcess([
                'ps' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'pe' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson'],
                    'errorMessage' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findProductCustomProperties(array $ids)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere('p.id.id in (:ids)', ['ids' => $ids])
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'cp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ]
            ])
            ->setJoins([
               'p' => [
                   ['customProperties', 'cp', 'surrogateId']
               ]
            ])
            ->setPostProcess([
                'cp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findSectionCustomProperties(array $ids)
    {
        $entityClass = 'Apto\Catalog\Domain\Core\Model\Product\Section\Section';
        $builder = new DqlQueryBuilder($entityClass);
        $builder
            ->setWhere('s.id.id in (:ids)', ['ids' => $ids])
            ->setValues([
                's' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'cp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ]
            ])
            ->setJoins([
                's' => [
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ])
            ->setPostProcess([
                'cp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findElementCustomProperties(array $ids)
    {
        $entityClass = 'Apto\Catalog\Domain\Core\Model\Product\Element\Element';
        $builder = new DqlQueryBuilder($entityClass);
        $builder
            ->setWhere('e.id.id in (:ids)', ['ids' => $ids])
            ->setValues([
                'e' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                's' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'cp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ]
            ])
            ->setJoins([
                'e' => [
                    ['section', 's', 'id'],
                    ['customProperties', 'cp', 'surrogateId']
                ],
                's' => [
                    ['product', 'p', 'id']
                ]
            ])
            ->setPostProcess([
                'cp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string $id
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNextSectionPosition(string $id) {
        $dql = 'SELECT
                  MAX(s.position) as max_position
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              WHERE
                  p.id.id = :id
              ';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('id', $id);
        $maxPosition = $query->getSingleScalarResult();

        if (!($maxPosition >= 0)) {
            return 0;
        }
        else {
            $newPosition = round($maxPosition/10) * 10;
            if ($newPosition <= $maxPosition) {
                return $newPosition + 10;
            }
            return $newPosition;
        }
    }

    /**
     * @param string $productId
     * @param string $sectionId
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNextElementPosition(string $productId, string $sectionId) {
        $dql = 'SELECT
                  MAX(e.position) as max_position
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              JOIN
                  s.elements e
              WHERE
                  p.id.id = :productId AND s.id.id = :sectionId
              ';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameters([
            'productId' => $productId,
            'sectionId' => $sectionId
        ]);
        $maxPosition = $query->getSingleScalarResult();

        if (!($maxPosition >= 0)) {
            return 0;
        }
        else {
            $newPosition = round($maxPosition/10) * 10;
            if ($newPosition <= $maxPosition) {
                return $newPosition + 10;
            }
            return $newPosition;
        }
    }
}