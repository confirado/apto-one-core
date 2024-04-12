<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Product\Element\ProductElementFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\Element;
use Apto\Catalog\Domain\Core\Model\Product\Element\RenderImageOptions;

class ProductElementOrmFinder extends AptoOrmFinder implements ProductElementFinder
{
    const ENTITY_CLASS = Element::class;

    /**
     * @param $value
     * @return array|null
     */
    public static function decodeRenderImageOptions($value): ?array
    {
        if(!$value) {
            return null;
        }

        /** @var RenderImageOptions $renderImageOptions */
        $renderImageOptions = unserialize($value);

        if($renderImageOptions instanceof RenderImageOptions) {
            return $renderImageOptions->jsonSerialize();
        }

        /** @phpstan-ignore-next-line  */
        return null;
    }

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
                'e' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'isDefault',
                    'isActive',
                    'isMandatory',
                    'isNotAvailable',
                    'isZoomable',
                    'priceMatrixActive',
                    'priceMatrixRow',
                    'priceMatrixColumn',
                    'extendedPriceCalculationActive',
                    'extendedPriceCalculationFormula',
                    'definition',
                    'position',
                    'name',
                    'description',
                    'errorMessage',
                    'percentageSurcharge',
                    ['zoomFunction.value', 'zoomFunction'],
                    'openLinksInDialog'
                ],
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'p' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'e' => [
                    ['previewImage', 'm', 'id'],
                    ['priceMatrix', 'p', 'id']
                ]
            ])
            ->setPostProcess([
                'e' => [
                    'isDefault' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isMandatory' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isNotAvailable' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isZoomable' => [DqlQueryBuilder::class, 'decodeBool'],
                    'priceMatrixActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'extendedPriceCalculationActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'definition' => [DqlQueryBuilder::class, 'decodeJson'],
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson'],
                    'errorMessage' => [DqlQueryBuilder::class, 'decodeJson'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'percentageSurcharge' => [DqlQueryBuilder::class, 'decodeFloat'],
                    'openLinksInDialog' => [DqlQueryBuilder::class, 'decodeBool']
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
            if(isset($result['priceMatrix'][0])) {
                $result['priceMatrix'] = $result['priceMatrix'][0];
            } else {
                $result['priceMatrix'] = null;
            }
        }

        return $result;
    }

    /**
     * @param array $ids
     * @return array
     * @throws \Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException
     */
    public function findElementsByIds(array $ids)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder->setWhere('e.id.id IN (:ids)', ['ids' => $ids]);
        $builder
            ->setValues([
                'e' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'isDefault',
                    'isActive',
                    'isMandatory',
                    'isNotAvailable',
                    'isZoomable',
                    'definition',
                    'position',
                    'name',
                    'description',
                    'errorMessage',
                    'percentageSurcharge',
                    ['zoomFunction.value', 'zoomFunction'],
                    'openLinksInDialog'
                ],
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
               'e' => [
                   ['previewImage', 'm', 'id']
               ]
            ])
            ->setPostProcess([
                 'e' => [
                     'isDefault' => [DqlQueryBuilder::class, 'decodeBool'],
                     'isActive' => [DqlQueryBuilder::class, 'decodeBool'],
                     'isMandatory' => [DqlQueryBuilder::class, 'decodeBool'],
                     'isNotAvailable' => [DqlQueryBuilder::class, 'decodeBool'],
                     'isZoomable' => [DqlQueryBuilder::class, 'decodeBool'],
                     'definition' => [DqlQueryBuilder::class, 'decodeJson'],
                     'name' => [DqlQueryBuilder::class, 'decodeJson'],
                     'description' => [DqlQueryBuilder::class, 'decodeJson'],
                     'errorMessage' => [DqlQueryBuilder::class, 'decodeJson'],
                     'position' => [DqlQueryBuilder::class, 'decodeInteger'],
                     'percentageSurcharge' => [DqlQueryBuilder::class, 'decodeFloat'],
                     'openLinksInDialog' => [DqlQueryBuilder::class, 'decodeBool']
                 ]
             ]);

        return $builder->getResult($this->entityManager)['data'];
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
                'e' => [
                ],
                'ep' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId'],
                    ['productConditionId', 'productConditionId'],
                ]
            ])
            ->setJoins([
                'e' => [
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
     */
    public function findPriceFormulas(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'e' => [
                ],
                'ef' => [
                    ['id.id', 'id'],
                    ['formula', 'formula'],
                    ['currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId'],
                    ['productConditionId', 'productConditionId'],
                ]
            ])
            ->setJoins([
                'e' => [
                    ['aptoPriceFormulas', 'ef', 'id']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return [];
        }
        return $result['aptoPriceFormulas'];
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
                'e' => [
                ],
                'ed' => [
                    ['id.id', 'id'],
                    'discount',
                    ['customerGroupId.id', 'customerGroupId'],
                    'name'
                ]
            ])
            ->setJoins([
                'e' => [
                    ['aptoDiscounts', 'ed', 'id']
                ]
            ])
            ->setPostProcess([
                'ed' => [
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
    public function findRenderImages(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'e' => [
                ],
                'r' => [
                    ['id.id', 'id'],
                    'layer',
                    'perspective',
                    'offsetX',
                    'offsetY',
                    'renderImageOptions'
                ],
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
                'e' => [
                    ['renderImages', 'r', 'id']
                ],
                'r' => [
                    ['mediaFile', 'm', 'id']
                ]
            ])
            ->setPostProcess([
                 'r' => [
                     'renderImageOptions' => [ProductElementOrmFinder::class, 'decodeRenderImageOptions']
                 ]
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
                'e' => [
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
                'e' => [
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param State $state
     * @param string $perspective
     * @return array
     */
    public function findRenderImagesByState(State $state, string $perspective)
    {
        $parameters = [
            'perspective' => $perspective,
            'elementIds' => $state->getElementIds()
        ];

        $dql = 'SELECT
                  p.id.id as productId,
                  r.id.id as renderImageId,
                  el.id.id as elementId,
                  s.id.id as sectionId,
                  r.layer,
                  r.perspective,
                  r.offsetX,
                  r.offsetY,
                  r.renderImageOptions,
                  m.file.directory.path as path,
                  m.file.filename as filename,
                  m.file.extension as extension
              FROM
                  ' . $this->entityClass . ' e
              LEFT JOIN
                  e.section s
              LEFT JOIN
                  s.product p
              LEFT JOIN
                  e.renderImages r
              LEFT JOIN
                  r.mediaFile m
              LEFT JOIN
                  r.element el
              WHERE
                  (e.id.id IN (:elementIds) AND r.perspective = :perspective)
              ORDER BY
                  r.layer ASC';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($parameters);

        $results = $query->getScalarResult();
        foreach($results as &$result) {
            $result['renderImageOptions'] = self::decodeRenderImageOptions($result['renderImageOptions']);
        }

        return $results;
    }

    /**
     * @param State $state
     * @return array
     */
    public function findElementDefinitionsByState(State $state)
    {
        $parameters = [
            'elementIds' => $state->getElementIds()
        ];

        $dql = 'SELECT
                  s.id.id as sectionId,
                  e.id.id as id,
                  e.identifier.value as identifier,
                  e.definition
              FROM
                  ' . $this->entityClass . ' e
              LEFT JOIN
                  e.section s
              WHERE
                  e.id.id IN (:elementIds)';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($parameters);

        return $query->getScalarResult();
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function findAttachments(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                            'e' => [
                            ],
                            'a' => [
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
                           'e' => [
                               ['attachments', 'a', 'id']
                           ],
                           'a' => [
                               ['mediaFile', 'm', 'id']
                           ]
                       ])
            ->setPostProcess([
                                 'a' => [
                                     'name' => [DqlQueryBuilder::class, 'decodeJson']
                                 ]
                             ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     *
     * @return array|void|null
     */
    public function findGallery(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'e' => [
                ],
                'a' => [
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
                'e' => [
                    ['gallery', 'a', 'id']
                ],
                'a' => [
                    ['mediaFile', 'm', 'id']
                ]
            ])
            ->setPostProcess([
                'a' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }
}
