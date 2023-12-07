<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Product\Section\ProductSectionFinder;
use Apto\Catalog\Domain\Core\Model\Product\Section\Section;

class ProductSectionOrmFinder extends AptoOrmFinder implements ProductSectionFinder
{
    const ENTITY_CLASS = Section::class;

    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                's' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'isActive',
                    'isHidden',
                    'isMandatory',
                    'isZoomable',
                    'allowMultiple',
                    ['repeatable.type', 'repeatableType'],
                    ['repeatable.calculatedValueName', 'repeatableCalculatedValueName'],
                    'position',
                    'name',
                    'description'
                ],
                'g' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
                's' => [
                    ['group', 'g', 'id'],
                    ['previewImage', 'm', 'id']
                ]
            ])
            ->setPostProcess([
                's' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson'],
                    'isActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isHidden' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isMandatory' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isZoomable' => [DqlQueryBuilder::class, 'decodeBool'],
                    'allowMultiple' => [DqlQueryBuilder::class, 'decodeBool'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ],
                'g' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if($result !== null) {
            if(isset($result['previewImage'][0])) {
                $result['previewImageMediaFile'] = $result['previewImage'][0];
                $result['previewImage'] = $result['previewImageMediaFile']['path'] . '/' . $result['previewImageMediaFile']['filename'] . '.' . $result['previewImageMediaFile']['extension'];
            } else {
                $result['previewImageMediaFile'] = $result['previewImage'];
                $result['previewImage'] = '';
            }
        }

        return $result;
    }

    /**
     * @param string $id
     * @return array
     */
    public function findPrices(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                's' => [
                ],
                'ep' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ]
            ])
            ->setJoins([
                's' => [
                    ['aptoPrices', 'ep', 'id']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return [];
        }
        return $result['aptoPrices'];
    }

    /**
     * @param string $id
     * @return array
     * @throws \Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException
     */
    public function findDiscounts(string $id): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                's' => [
                ],
                'sd' => [
                    ['id.id', 'id'],
                    'discount',
                    ['customerGroupId.id', 'customerGroupId'],
                    'name'
                ]
            ])
            ->setJoins([
                's' => [
                    ['aptoDiscounts', 'sd', 'id']
                ]
            ])
            ->setPostProcess([
                'sd' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return [];
        }
        return $result['aptoDiscounts'];
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function findElements(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                's' => [
                ],
                'e' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'isActive',
                    'isDefault',
                    'isMandatory',
                    'isNotAvailable',
                    'position',
                    'name'
                ]
            ])
            ->setJoins([
                's' => [
                    ['elements', 'e', 'id']
                ]
            ])
            ->setPostProcess([
                'e' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'isActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isDefault' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isMandatory' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isNotAvailable' => [DqlQueryBuilder::class, 'decodeBool'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ])->setOrderBy([
                ['e.position', 'ASC']
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function findCustomProperties(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                's' => [
                ],
                'cp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ]
            ])
            ->setPostProcess([
                'cp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setJoins([
                's' => [
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }
}
