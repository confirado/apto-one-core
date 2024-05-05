<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlPaginatorBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Dql\ProductDqlService;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Doctrine\ORM\EntityManagerInterface;

class ProductOrmFinder extends AptoOrmFinder implements ProductFinder
{
    const ENTITY_CLASS = Product::class;

    const PRODUCT_ALL_VALUES = [
        ['id.id', 'id'],
        ['identifier.value', 'identifier'],
        'seoUrl',
        'name',
        'description',
        'active',
        'hidden',
        'useStepByStep',
        'keepSectionOrder',
        'articleNumber',
        'metaTitle',
        'metaDescription',
        'stock',
        'minPurchase',
        'maxPurchase',
        'deliveryTime',
        'weight',
        'taxRate',
        'created',
        'priceCalculatorId',
        'position'
    ];

    const PRODUCT_ALL_POST_PROCESSES = [
        'name' => [DqlQueryBuilder::class, 'decodeJson'],
        'description' => [DqlQueryBuilder::class, 'decodeJson'],
        'active' => [DqlQueryBuilder::class, 'decodeBool'],
        'hidden' => [DqlQueryBuilder::class, 'decodeBool'],
        'useStepByStep' => [DqlQueryBuilder::class, 'decodeBool'],
        'keepSectionOrder' => [DqlQueryBuilder::class, 'decodeBool'],
        'metaTitle' => [DqlQueryBuilder::class, 'decodeJson'],
        'metaDescription' => [DqlQueryBuilder::class, 'decodeJson'],
        'stock' => [DqlQueryBuilder::class, 'decodeInteger'],
        'minPurchase' => [DqlQueryBuilder::class, 'decodeInteger'],
        'maxPurchase' => [DqlQueryBuilder::class, 'decodeInteger'],
        'weight' => [DqlQueryBuilder::class, 'decodeFloat'],
        'taxRate' => [DqlQueryBuilder::class, 'decodeFloat'],
        'position' => [DqlQueryBuilder::class, 'decodeInteger']
    ];

    /**
     * @var string
     */
    protected $sectionEntityClass;

    /**
     * PoolOrmFinder constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->sectionEntityClass = 'Apto\Catalog\Domain\Core\Model\Product\Section\Section';
    }

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
                'p' => self::PRODUCT_ALL_VALUES,
                's' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'c' => [
                    ['id.id', 'id'],
                    'name',
                ],
                // product section
                'ps' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'name',
                    'isActive',
                    'isHidden',
                    'isMandatory',
                    'isZoomable',
                    'allowMultiple',
                    ['repeatable.type', 'repeatableType'],
                    ['repeatable.calculatedValueName', 'repeatableCalculatedValueName'],
                ],
                // product element
                'pe' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'isDefault',
                    'isActive',
                    'isMandatory',
                    'isNotAvailable',
                    'isZoomable',
                    'definition',
                    'name',
                    'errorMessage',
                    ['zoomFunction.value', 'zoomFunction'],
                    'openLinksInDialog'
                ],
                'pdp' => [
                    ['id.id', 'id'],
                    'priceModifier'
                ],
                'pdps' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'pdpm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'fp' => [
                    ['id.id', 'id'],
                    'name'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['shops', 's', 'id'],
                    ['categories', 'c', 'id'],
                    ['sections', 'ps', 'id'],
                    ['previewImage', 'm', 'id'],
                    ['filterProperties', 'fp', 'id'],
                    ['domainProperties', 'pdp', 'id']
                ],
                'ps' => [
                    ['elements', 'pe', 'id']
                ],
                'pdp' => [
                    ['shop', 'pdps', 'id'],
                    ['previewImage', 'pdpm', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => self::PRODUCT_ALL_POST_PROCESSES,
                'c' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'ps' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'isActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isHidden' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isMandatory' => [DqlQueryBuilder::class, 'decodeBool'],
                    'allowMultiple' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isZoomable' => [DqlQueryBuilder::class, 'decodeBool']
                ],
                'pe' => [
                    'isDefault' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isMandatory' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isNotAvailable' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isZoomable' => [DqlQueryBuilder::class, 'decodeBool'],
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'errorMessage' => [DqlQueryBuilder::class, 'decodeJson'],
                    'definition' => [DqlQueryBuilder::class, 'decodeJson'],
                    'openLinksInDialog' => [DqlQueryBuilder::class, 'decodeBool']
                ],
                'fp' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'pdp' => [
                    'priceModifier' => [DqlQueryBuilder::class, 'decodeFloat']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if ($result !== null) {
            if(isset($result['previewImage'][0])) {
                $result['previewImageMediaFile'] = $result['previewImage'][0];
                $result['previewImage'] = $result['previewImageMediaFile']['path'] . '/' . $result['previewImageMediaFile']['filename'] . '.' . $result['previewImageMediaFile']['extension'];
            } else {
                $result['previewImageMediaFile'] = $result['previewImage'];
                $result['previewImage'] = '';
            }
            foreach ($result['domainProperties'] as &$domainProperties) {
                if(isset($domainProperties['shop'][0])) {
                    $domainProperties['shop'] = $domainProperties['shop'][0];
                }
                if(isset($domainProperties['previewImage'][0])) {
                    $domainProperties['previewImageMediaFile'] = $domainProperties['previewImage'][0];
                    $domainProperties['previewImage'] = $domainProperties['previewImageMediaFile']['path'] . '/' . $domainProperties['previewImageMediaFile']['filename'] . '.' . $domainProperties['previewImageMediaFile']['extension'];
                } else {
                    $domainProperties['previewImageMediaFile'] = $domainProperties['previewImage'];
                    $domainProperties['previewImage'] = '';
                }
            }
        }

        return $result;
    }

    /**
     * @param string $seoUrl
     * @param bool $withRules
     * @param bool $withComputedValues
     * @return mixed|null
     * @throws DqlBuilderException
     */
    public function findConfigurableProductBySeoUrl(string $seoUrl, bool $withRules = true, bool $withComputedValues = true)
    {
        return $this->findConfigurableProduct('seoUrl', $seoUrl, $withRules, $withComputedValues);
    }

    /**
     * @param string $id
     * @param bool $withRules
     * @param bool $withComputedValues
     * @return mixed|null
     * @throws DqlBuilderException
     */
    public function findConfigurableProductById(string $id, bool $withRules = true, bool $withComputedValues = true)
    {
        return $this->findConfigurableProduct('id.id', $id, $withRules, $withComputedValues);
    }

    /**
     * @param string $field
     * @param string $value
     * @param bool $withRules
     * @param bool $withComputedValues
     * @return array|null
     * @throws DqlBuilderException
     */
    protected function findConfigurableProduct(string $field, string $value, bool $withRules = true, bool $withComputedValues = true)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere('p.' . $field . ' = :configurableProductSearchParam AND p.active = :activeFlag', ['configurableProductSearchParam' => $value, 'activeFlag' => true])
            ->setValues([
                'p' => self::PRODUCT_ALL_VALUES,
                'pcp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ],
                //section
                's' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'position',
                    'name',
                    'description',
                    'isActive',
                    'isHidden',
                    'isZoomable',
                    'isMandatory',
                    'allowMultiple',
                    ['repeatable.type', 'repeatableType'],
                    ['repeatable.calculatedValueName', 'repeatableCalculatedValueName'],
                ],
                // element
                'e' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'isDefault',
                    'isMandatory',
                    'isNotAvailable',
                    'isZoomable',
                    'definition',
                    'position',
                    'name',
                    'description',
                    'errorMessage',
                    ['zoomFunction.value', 'zoomFunction'],
                    'openLinksInDialog'
                ],
                'sg' => [
                    ['id.id', 'id'],
                    'name',
                    'position',
                    ['identifier.value', 'identifier']
                ],
                'epi' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'spi' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'scp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ],
                'ecp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ],
                'cat' => [
                    ['id.id', 'id'],
                    'name',
                    'description'
                ],
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'ea' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'ga' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'eam' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'eamg' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'pdp' => [
                    ['id.id', 'id']
                ],
                'pdps' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'pdpm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'ps' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'p' => [
                    [
                        'sections',
                        's',
                        'id',
                        's.isActive = 1 AND s.surrogateId IN (
                            SELECT DISTINCT sSub.surrogateId
                            FROM ' . $this->sectionEntityClass . ' sSub
                            JOIN sSub.elements eSub
                            WHERE eSub.isActive = 1
                        )'
                    ],
                    ['categories', 'cat', 'id'],
                    ['customProperties', 'pcp', 'surrogateId'],
                    ['previewImage', 'm', 'id'],
                    ['domainProperties', 'pdp', 'id'],
                    ['shops', 'ps', 'id']
                ],
                's' => [
                    ['previewImage', 'spi', 'id'],
                    ['elements', 'e', 'id', 'e.isActive = 1'],
                    ['group', 'sg', 'id'],
                    ['customProperties', 'scp', 'surrogateId']
                ],
                'e' => [
                    ['previewImage', 'epi', 'id'],
                    ['customProperties', 'ecp', 'surrogateId'],
                    ['gallery', 'ga', 'id'],
                    ['attachments', 'ea', 'id']
                ],
                'ea' => [
                    ['mediaFile', 'eam', 'id']
                ],
                'ga' => [
                    ['mediaFile', 'eamg', 'id']
                ],
                'pdp' => [
                    ['shop', 'pdps', 'id'],
                    ['previewImage', 'pdpm', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => self::PRODUCT_ALL_POST_PROCESSES,
                's' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson'],
                    'isActive' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isHidden' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isMandatory' => [DqlQueryBuilder::class, 'decodeBool'],
                    'allowMultiple' => [DqlQueryBuilder::class, 'decodeBool'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'isZoomable' => [DqlQueryBuilder::class, 'decodeBool']
                ],
                'e' => [
                    'isDefault' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isMandatory' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isNotAvailable' => [DqlQueryBuilder::class, 'decodeBool'],
                    'isZoomable' => [DqlQueryBuilder::class, 'decodeBool'],
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson'],
                    'errorMessage' => [DqlQueryBuilder::class, 'decodeJson'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'openLinksInDialog' => [DqlQueryBuilder::class, 'decodeBool']
                ],
                'sg' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ],
                'cat' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'pcp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ],
                'scp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ],
                'ecp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ],
                'ea' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ])->setOrderBy([
                ['s.position', 'ASC'],
                ['s.identifier.value', 'ASC'],
                ['e.position', 'ASC'],
                ['e.identifier.value', 'ASC']
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if ($result !== null) {
            if (isset($result['previewImage'][0])) {
                $result['previewImageMediaFile'] = $result['previewImage'][0];
                $result['previewImage'] = $result['previewImageMediaFile']['path'] . '/' . $result['previewImageMediaFile']['filename'] . '.' . $result['previewImageMediaFile']['extension'];
            } else {
                $result['previewImageMediaFile'] = $result['previewImage'];
                $result['previewImage'] = '';
            }

            foreach ($result['domainProperties'] as &$domainProperties) {
                if(isset($domainProperties['shop'][0])) {
                    $domainProperties['shop'] = $domainProperties['shop'][0];
                }
                if(isset($domainProperties['previewImage'][0])) {
                    $domainProperties['previewImageMediaFile'] = $domainProperties['previewImage'][0];
                    $domainProperties['previewImage'] = $domainProperties['previewImageMediaFile']['path'] . '/' . $domainProperties['previewImageMediaFile']['filename'] . '.' . $domainProperties['previewImageMediaFile']['extension'];
                } else {
                    $domainProperties['previewImageMediaFile'] = $domainProperties['previewImage'];
                    $domainProperties['previewImage'] = '';
                }
            }
        }

        // skip further queries if product not found
        if ($result === null) {
            return null;
        }

        // get rules
        if ($withRules) {
            $result['rules'] = $this->getProductRules($field, $value);
        }

        // get computed values
        if ($withComputedValues) {
            $result['computedValues'] = $this->getComputedValues($field, $value);
        }

        return $result;
    }

    /**
     * @param string $field
     * @param string $value
     * @return array
     * @throws DqlBuilderException
     */
    protected function getProductRules(string $field, string $value): array
    {
        $ruleBuilder = new DqlQueryBuilder($this->entityClass);
        $ruleBuilder
            ->setWhere('p.' . $field . ' = :configurableProductSearchParam', ['configurableProductSearchParam' => $value])
            ->setValues([
                'p' => [],
                'r' => [
                    ['id.id', 'id'],
                    'active',
                    'name',
                    'errorMessage',
                    'conditionsOperator',
                    'implicationsOperator',
                    'softRule',
                    'description',
                    'position',
                ],
                'c' => [
                    ['id.id', 'id'],
                    'type',
                    'sectionId',
                    'elementId',
                    'property',
                    ['operator.operator', 'operator'],
                    'value'
                ],
                'i' => [
                    ['id.id', 'id'],
                    'type',
                    'sectionId',
                    'elementId',
                    'property',
                    ['operator.operator', 'operator'],
                    'value'
                ],
                'ccpv' => [
                    ['id.id', 'id']
                ],
                'icpv' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['rules', 'r', 'id', 'r.active = 1']
                ],
                'r' => [
                    ['conditions', 'c', 'id'],
                    ['implications', 'i', 'id']
                ],
                'c' => [
                    ['computedProductValue', 'ccpv', 'id']
                ],
                'i' => [
                    ['computedProductValue', 'icpv', 'id']
                ]
            ])
            ->setPostProcess([
                'r' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool'],
                    'errorMessage' => [DqlQueryBuilder::class, 'decodeJson'],
                    'conditionsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'implicationsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'softRule' => [DqlQueryBuilder::class, 'decodeBool'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger'],
                ],
                'c' => [
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ],
                'i' => [
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ])
            ->setOrderBy([
                ['r.position', 'ASC']
            ]);

        $result = $ruleBuilder->getSingleResultOrNull($this->entityManager);
        return $result ? $result['rules'] : [];
    }

    /**
     * @param string $field
     * @param string $value
     * @return array
     * @throws DqlBuilderException
     */
    protected function getProductConditions(string $field, string $value): array
    {
        $conditionBuilder = new DqlQueryBuilder($this->entityClass);
        $conditionBuilder
            ->setWhere('p.' . $field . ' = :configurableProductSearchParam', ['configurableProductSearchParam' => $value])
            ->setValues([
                'p' => [],
                'pc' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'type',
                    'sectionId',
                    'elementId',
                    'property',
                    'operator',
                    'value'
                ],
                'ccpv' => [
                    ['id.id', 'id']
                ],
                'icpv' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['conditions', 'pc', 'id']
                ],
                'pc' => [
                    ['computedProductValue', 'ccpv', 'id']
                ]
            ])
            ->setPostProcess([
                'pc' => [
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);

        $result = $conditionBuilder->getSingleResultOrNull($this->entityManager);
        return $result ? $result['conditions'] : [];
    }

    /**
     * @param string $field
     * @param string $value
     * @return array
     * @throws DqlBuilderException
     */
    protected function getComputedValues(string $field, string $value): array
    {
        $ruleBuilder = new DqlQueryBuilder($this->entityClass);
        $ruleBuilder
            ->setWhere('p.' . $field . ' = :configurableProductSearchParam', ['configurableProductSearchParam' => $value])
            ->setValues([
                'p' => [],
                'cpv' => [
                    ['id.id', 'id'],
                    'formula',
                    'name'
                ],
                'cpva' => [
                    ['id.id', 'id'],
                    'name',
                    'sectionId',
                    'elementId',
                    'property'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['computedProductValues', 'cpv', 'id']
                ],
                'cpv' => [
                    ['aliases', 'cpva', 'id']
                ]
            ]);

        $result = $ruleBuilder->getSingleResultOrNull($this->entityManager);
        return $result ? $result['computedProductValues'] : [];
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findSectionsElements(string $id)
    {
        $dqlService = new ProductDqlService($this->entityManager, $this->entityClass);
        return $dqlService->findSectionsElements($id);
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findProductCustomProperties(array $ids)
    {
        $dqlService = new ProductDqlService($this->entityManager, $this->entityClass);
        return $dqlService->findProductCustomProperties($ids);
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findSectionCustomProperties(array $ids)
    {
        $dqlService = new ProductDqlService($this->entityManager, $this->entityClass);
        return $dqlService->findSectionCustomProperties($ids);
    }

    /**
     * @param array $ids
     * @return array
     * @throws DqlBuilderException
     */
    public function findElementCustomProperties(array $ids)
    {
        $dqlService = new ProductDqlService($this->entityManager, $this->entityClass);
        return $dqlService->findElementCustomProperties($ids);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findSections(string $id)
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
                    'allowMultiple',
                    ['repeatable.type', 'repeatableType'],
                    ['repeatable.calculatedValueName', 'repeatableCalculatedValueName'],
                ]
            ])
            ->setJoins([
                'p' => [
                    ['sections', 'ps', 'id']
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
                ]
            ])->setOrderBy([
                ['ps.position', 'ASC']
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findRules(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'pr' => [
                    ['id.id', 'id'],
                    'active',
                    'name',
                    'errorMessage',
                    'conditionsOperator',
                    'implicationsOperator',
                    'softRule',
                    'description',
                    'position',
                ]
            ])
            ->setJoins([
                'p' => [
                    ['rules', 'pr', 'id']
                ]
            ])
            ->setPostProcess([
                'pr' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool'],
                    'errorMessage' => [DqlQueryBuilder::class, 'decodeJson'],
                    'conditionsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'implicationsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'softRule' => [DqlQueryBuilder::class, 'decodeBool'],
                    'position' => [DqlQueryBuilder::class, 'decodeInteger'],
                ]
            ])
            ->setOrderBy([
                ['pr.position', 'ASC']
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findProductConditionSets(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'pcs' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'conditionsOperator',
                ]
            ])
            ->setJoins([
                'p' => [
                    ['conditionSets', 'pcs', 'id']
                ]
            ])
            ->setPostProcess([
                'c' => [
                    'conditionsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                ]
            ])
        ;

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findProductConditions(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'pc' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'sectionId',
                    'elementId',
                    'property',
                    ['operator.operator', 'operator'],
                    'value',
                    'type'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['conditions', 'pc', 'id']
                ]
            ])
            ->setPostProcess([
                'pc' => [
                    'value' => [DqlQueryBuilder::class, 'castString'],
                    'type' => [DqlQueryBuilder::class, 'decodeInteger']
                ]
            ]);
        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findComputedValues(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'pc' => [
                    ['id.id', 'id'],
                    'name',
                    'formula'
                ],
                'pca' => [
                    ['id.id', 'id'],
                    'sectionId',
                    'elementId',
                    'name',
                    'property'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['computedProductValues', 'pc', 'id']
                ],
                'pc' => [
                    ['aliases', 'pca', 'id']
                ]
            ]);
        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findPrices(string $id): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'pp' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId'],
                    ['productConditionId', 'productConditionId'],
                ]
            ])
            ->setJoins([
                'p' => [
                    ['aptoPrices', 'pp', 'id']
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
     * @throws DqlBuilderException
     */
    public function findDiscounts(string $id): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'pd' => [
                    ['id.id', 'id'],
                    'discount',
                    ['customerGroupId.id', 'customerGroupId'],
                    'name'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['aptoDiscounts', 'pd', 'id']
                ]
            ])
            ->setPostProcess([
                'pd' => [
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
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 50, string $searchString = ''): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setPage($pageNumber)
            ->setRecordsPerPage($recordsPerPage)
            ->setValues([
                'p' => self::PRODUCT_ALL_VALUES,
                's' => [
                    ['id.id', 'id'],
                    'name'
                ]
            ])
            ->setSearch([
                'p' => [
                    'id.id',
                    'name'
                ]
            ], $searchString)
            ->setJoins([
                'p' => [
                    ['shops', 's', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => self::PRODUCT_ALL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['p.position', 'ASC'],
                ['p.created', 'DESC']
            ]);

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param string|null $categoryIdentifier
     * @return array
     * @throws DqlBuilderException
     */
    public function findByCategoryIdentifier(string $categoryIdentifier = null): array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => self::PRODUCT_ALL_VALUES,
                'c' => [
                    ['id.id', 'id'],
                    //['identifier.value', 'identifier'] @todo add identifier to category
                ]
            ])
            ->setJoins([
                'p' => [
                    ['categories', 'c', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => self::PRODUCT_ALL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['p.identifier.value', 'ASC']
            ]);

        /*
         * @todo add identifier to category
        if ($categoryIdentifier !== '') {
            $builder->setWhere('c.identifier.value = :identifier', [
                'identifier' => $categoryIdentifier
            ]);
        }
        */

        return $builder->getResult($this->entityManager);
    }

    /**
     * @param array $filter
     * @param bool $onlyActive
     * @return array
     * @throws DqlBuilderException
     */
    public function findByFilter(array $filter = [], bool $onlyActive = true): array
    {
        $productIds = $this->findAllProductIdsByCategories($filter, $onlyActive);

        // set default where
        $where = [
            'where' => '',
            'parameters' => []
        ];

        if ($onlyActive) {
            $where['where'] = 'p.active = :active';
            $where['parameters'] = [
                'active' => true
            ];
        }

        // set default order
        $orderBy = [
            ['p.position', 'ASC'],
            ['p.identifier.value', 'ASC']
        ];

        // add where
        $where['where'] .= ' AND p.id.id IN (:products)';
        $where['parameters']['products'] = $productIds;

        // create query
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere($where['where'], $where['parameters'])
            ->setValues([
                'p' => self::PRODUCT_ALL_VALUES,
                'pcp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ],
                'cat' => [
                    ['id.id', 'id'],
                    'name',
                    'description'
                ],
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'fp' => [
                    ['id.id', 'id']
                ],
                'pdp' => [
                    ['id.id', 'id']
                ],
                'pdps' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'pdpm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ],
                'ps' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['categories', 'cat', 'id'],
                    ['customProperties', 'pcp', 'surrogateId'],
                    ['previewImage', 'm', 'id'],
                    ['filterProperties', 'fp', 'id'],
                    ['domainProperties', 'pdp', 'id'],
                    ['shops', 'ps', 'id']
                ],
                'pdp' => [
                    ['shop', 'pdps', 'id'],
                    ['previewImage', 'pdpm', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => self::PRODUCT_ALL_POST_PROCESSES,
                'cat' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'pcp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setOrderBy($orderBy);

        if (array_key_exists('searchString', $filter)) {
            $builder->setSearch([
                'p' => [
                    'id.id',
                    'identifier.value',
                    'articleNumber',
                    'seoUrl',
                    'name',
                    'description'
                ]
            ], $filter['searchString']);
        }

        // return result
        $results = $builder->getResult($this->entityManager);

        foreach ($results['data'] as $i=>$result){
            if(isset($result['previewImage'][0])) {
                $result['previewImageMediaFile'] = $result['previewImage'][0];
                $results['data'][$i]['previewImage'] = $result['previewImageMediaFile']['path'] . '/' . $result['previewImageMediaFile']['filename'] . '.' . $result['previewImageMediaFile']['extension'];
            } else {
                $result['previewImageMediaFile'] = $result['previewImage'];
                $results['data'][$i]['previewImage'] = '';
            }
            // flatten filterProperty Ids
            $propertyIds = [];
            foreach ($result['filterProperties'] as $propertyId) {
                $propertyIds[] = $propertyId['id'];
            }
            $results['data'][$i]['filterProperties'] = $propertyIds;
            foreach ($result['domainProperties'] as &$domainProperties) {
                if(isset($domainProperties['shop'][0])) {
                    $domainProperties['shop'] = $domainProperties['shop'][0];
                }
                if(isset($domainProperties['previewImage'][0])) {
                    $domainProperties['previewImageMediaFile'] = $domainProperties['previewImage'][0];
                    $domainProperties['previewImage'] = $domainProperties['previewImageMediaFile']['path'] . '/' . $domainProperties['previewImageMediaFile']['filename'] . '.' . $domainProperties['previewImageMediaFile']['extension'];
                } else {
                    $domainProperties['previewImageMediaFile'] = $domainProperties['previewImage'];
                    $domainProperties['previewImage'] = '';
                }
            }
            $results['data'][$i]['domainProperties'] = $result['domainProperties'];
        }

        return $results;
    }

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param array $filter
     * @param bool $onlyActive
     * @return array
     * @throws DqlBuilderException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByFilterPagination(int $pageNumber = 1, int $recordsPerPage = 50, array $filter = [], bool $onlyActive = true): array
    {
        $productIds = $this->findAllProductIdsByCategories($filter, $onlyActive);

        // set default where
        $where = [
            'where' => '',
            'parameters' => []
        ];

        if ($onlyActive) {
            $where['where'] = 'p.active = :active';
            $where['parameters'] = [
                'active' => true
            ];
        }

        // add where
        if ($where['where'] !== '') {
            $where['where'] .= ' AND';
        }
        $where['where'] .= 'p.id.id IN (:products)';
        $where['parameters']['products'] = $productIds;

        // create query
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setPage($pageNumber)
            ->setRecordsPerPage($recordsPerPage)
            ->setWhere($where['where'], $where['parameters'])
            ->setValues([
                'p' => self::PRODUCT_ALL_VALUES,
                'pcp' => [
                    'surrogateId',
                    'key',
                    'value',
                    'translatable'
                ],
                'cat' => [
                    ['id.id', 'id'],
                    'name',
                    'description'
                ],
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setSearch([
                'p' => [
                    'id.id',
                    'name'
                ]
            ], $filter['searchString'])
            ->setJoins([
                'p' => [
                    ['categories', 'cat', 'id'],
                    ['customProperties', 'pcp', 'surrogateId'],
                    ['previewImage', 'm', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => self::PRODUCT_ALL_POST_PROCESSES,
                'cat' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson']
                ],
                'pcp' => [
                    'value' => [DqlQueryBuilder::class, 'decodeCustomPropertyValue'],
                    'translatable' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ])
            ->setOrderBy([
                ['p.position', 'ASC'],
                ['p.created', 'DESC']
            ]);

        // return result
        $results = $builder->getResult($this->entityManager);

        foreach ($results['data'] as $i=>$result){
            if(isset($result['previewImage'][0])) {
                $result['previewImageMediaFile'] = $result['previewImage'][0];
                $results['data'][$i]['previewImage'] = $result['previewImageMediaFile']['path'] . '/' . $result['previewImageMediaFile']['filename'] . '.' . $result['previewImageMediaFile']['extension'];
            } else {
                $result['previewImageMediaFile'] = $result['previewImage'];
                $results['data'][$i]['previewImage'] = '';
            }
        }
        return $results;
    }

    /**
     * @param array $filter
     * @param bool $onlyActive
     * @return array
     * @throws DqlBuilderException
     */
    public function findProductIdsByFilter(array $filter = [], bool $onlyActive = true): array
    {
        $productIds = $this->findAllProductIdsByCategories($filter, $onlyActive);

        // set default where
        $where = [
            'where' => '',
            'parameters' => []
        ];

        // add active filter
        if ($onlyActive) {
            $where['where'] = 'p.active = :active';
            $where['parameters'] = [
                'active' => true
            ];
        }

        // if where is not empty add AND operator
        if ($where['where'] !== '') {
            $where['where'] .= ' AND';
        }

        // add product filter
        $where['where'] .= 'p.id.id IN (:products)';
        $where['parameters']['products'] = $productIds;

        // create query
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere($where['where'], $where['parameters'])
            ->setValues([
                'p' => [
                    ['id.id', 'id']
                ]
            ])
            ->setOrderBy([
                 ['p.position', 'ASC'],
                 ['p.created', 'DESC']
            ]);

        if (array_key_exists('searchString', $filter)) {
            $builder->setSearch([
                'p' => [
                    'id.id',
                    'name'
                ]
            ], $filter['searchString']);
        }

        // create result
        $result = [];
        foreach ($builder->getResult($this->entityManager)['data'] as $product){
            $result[] = $product['id'];
        }

        // return result
        return $result;
    }

    /**
     * @param array $filter
     * @param bool $onlyActive
     * @return array
     * @throws DqlBuilderException
     */
    public function findAllProductIdsByCategories(array $filter,bool $onlyActive): array
    {
        // init where array
        $where = [
            'where' => '',
            'parameters' => []
        ];

        // add active filter
        if ($onlyActive) {
            $where['where'] = 'p.active = :active';
            $where['parameters'] = [
                'active' => true
            ];
        }

        // add category filter
        if (array_key_exists('categories', $filter) && count($filter['categories']) > 0) {
            // if where is not empty add AND operator
            if ($where['where'] !== '') {
                $where['where'] .= ' AND ';
            }

            // add where
            $where['where'] .= 'cat.id.id IN (:categories)';
            $where['parameters']['categories'] = $filter['categories'];
        }

        // create query
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setWhere($where['where'], $where['parameters'])
            ->setValues([
                'p' => [
                    ['id.id', 'id']
                ],
                'cat' => [
                    ['id.id', 'id']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['categories', 'cat', 'id']
                ]
            ]);

        // create result
        $result = [];
        foreach ($builder->getResult($this->entityManager)['data'] as $product) {
            $result[] = $product['id'];
        }

        // return result
        return $result;
    }

    /**
     * @param string $identifier
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findProductByIdentifier(string $identifier)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'ps' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'pe' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'ri' => [
                    ['id.id', 'id'],
                    'layer',
                    'perspective'
                ],
                'mf' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'path'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['sections', 'ps', 'id']
                ],
                'ps' => [
                    ['elements', 'pe', 'id']
                ],
                'pe' => [
                    ['renderImages', 'ri', 'id']
                ],
                'ri' => [
                    ['mediaFile', 'mf', 'id']
                ]
            ])
            ->setWhere('p.identifier.value = :identifier', [
                'identifier' => $identifier
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $productIdentifier
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findProductIdByIdentifier(string $productIdentifier)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ]
            ])
            ->setWhere('p.identifier.value = :productIdentifier',
                [
                    'productIdentifier' => $productIdentifier
                ]
            );

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findCustomProperties(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
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
                'p' => [
                    ['customProperties', 'cp', 'surrogateId']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $productIdentifier
     * @param string $sectionIdentifier
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findSectionIdByIdentifier(string $productIdentifier, string $sectionIdentifier)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'ps' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['sections', 'ps', 'id']
                ]
            ])
            ->setWhere('p.identifier.value = :productIdentifier AND ps.identifier.value = :sectionIdentifier',
                [
                    'productIdentifier' => $productIdentifier,
                    'sectionIdentifier' => $sectionIdentifier
                ]
            );

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $productIdentifier
     * @param string $sectionIdentifier
     * @param string $elementIdentifier
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findElementIdByIdentifier(string $productIdentifier, string $sectionIdentifier, string $elementIdentifier)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'ps' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'pe' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
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
            ->setWhere('p.identifier.value = :productIdentifier AND ps.identifier.value = :sectionIdentifier AND pe.identifier.value = :elementIdentifier',
                [
                    'productIdentifier' => $productIdentifier,
                    'sectionIdentifier' => $sectionIdentifier,
                    'elementIdentifier' => $elementIdentifier
                ]
            );

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @param State $state
     * @param string $currencyCode
     * @param string $fallbackCurrencyCode
     * @param string $customerGroupId
     * @param string|null $fallbackCustomerGroupId
     * @param string|null $shopId
     * @return array
     */
    public function findPricesByState(string $id, State $state, string $currencyCode, string $fallbackCurrencyCode, string $customerGroupId, string $fallbackCustomerGroupId = null, string $shopId = null)
    {
        $sectionIds = array_keys($state->getSectionIds());
        $elementIds = $state->getElementIds();
        $customerGroupIds = null !== $fallbackCustomerGroupId ? [$customerGroupId, $fallbackCustomerGroupId] : [$customerGroupId];
        $currencyCodes = [$currencyCode, $fallbackCurrencyCode];

        $priceMultiplierParameters = [
            'productId' => $id,
            'shopId' => $shopId
        ];

        $productPriceParameters = [
            'productId' => $id,
            'customerGroupIds' => $customerGroupIds,
            'currencyCodes' => $currencyCodes
        ];

        $sectionPriceParameters = [
            'productId' => $id,
            'sectionIds' => $sectionIds,
            'customerGroupIds' => $customerGroupIds,
            'currencyCodes' => $currencyCodes
        ];

        $elementPriceParameters = [
            'productId' => $id,
            'elementIds' => $elementIds,
            'customerGroupIds' => $customerGroupIds,
            'currencyCodes' => $currencyCodes
        ];

        $elementPriceMatrixParameters = [
            'productId' => $id,
            'elementIds' => $elementIds
        ];

        $elementPriceFormulaParameters = [
            'productId' => $id,
            'elementIds' => $elementIds,
            'customerGroupIds' => $customerGroupIds,
            'currencyCodes' => $currencyCodes
        ];

        $elementPercentageSurchargeParameters = [
            'productId' => $id,
            'elementIds' => $elementIds
        ];

        $elementDefinitionsParameters = [
            'productId' => $id,
            'elementIds' => $elementIds
        ];

        $productDiscountParameters = [
            'productId' => $id,
            'customerGroupId' => $customerGroupId
        ];

        $sectionDiscountParameters = [
            'productId' => $id,
            'sectionIds' => $sectionIds,
            'customerGroupId' => $customerGroupId
        ];

        $elementDiscountParameters = [
            'productId' => $id,
            'elementIds' => $elementIds,
            'customerGroupId' => $customerGroupId
        ];
        $dqlPriceModifier = 'SELECT
                  p.id.id as productId,
                  pdp.priceModifier as priceModifier,
                  pdps.id.id as shopId
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.domainProperties pdp
              JOIN
                  pdp.shop pdps
              WHERE
                  p.id.id = :productId AND pdps.id.id = :shopId
              ';

        $dqlProductPrices = 'SELECT
                  p.id.id as productId,
                  pp.price.amount as amount,
                  pp.price.currency.code as currencyCode,
                  pp.customerGroupId.id as customerGroupId,
                  pp.productConditionId as productConditionId
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.aptoPrices pp
              WHERE
                  p.id.id = :productId AND pp.price.currency.code IN (:currencyCodes) AND pp.customerGroupId.id IN (:customerGroupIds)
              ';

        $dqlSectionPrices = 'SELECT
                  s.id.id as sectionId,
                  sp.price.amount as amount,
                  sp.price.currency.code as currencyCode,
                  sp.customerGroupId.id as customerGroupId
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              JOIN
                  s.aptoPrices sp
              WHERE
                  (p.id.id = :productId AND s.id.id IN (:sectionIds) AND sp.price.currency.code IN (:currencyCodes) AND sp.customerGroupId.id IN (:customerGroupIds))
              ';

        $dqlElementPrices = 'SELECT
                  e.id.id as elementId,
                  e.extendedPriceCalculationActive as extendedPriceCalculationActive,
                  e.extendedPriceCalculationFormula as extendedPriceCalculationFormula,
                  ep.price.amount as amount,
                  ep.price.currency.code as currencyCode,
                  ep.customerGroupId.id as customerGroupId
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              JOIN
                  s.elements e
              JOIN
                  e.aptoPrices ep
              WHERE
                  (p.id.id = :productId AND e.id.id IN (:elementIds) AND ep.price.currency.code IN (:currencyCodes) AND ep.customerGroupId.id IN (:customerGroupIds))
              ';

        $dqlElementPriceMatrix = 'SELECT
                  e.id.id as elementId,
                  e.priceMatrixActive as priceMatrixActive,
                  pm.id.id as priceMatrixId,
                  e.priceMatrixRow as priceMatrixRow,
                  e.priceMatrixColumn as priceMatrixColumn
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              JOIN
                  s.elements e
              JOIN
                  e.priceMatrix pm
              WHERE
                  (p.id.id = :productId AND e.id.id IN (:elementIds))
              ';

        $dqlElementPriceFormulas = 'SELECT
                  e.id.id as elementId,
                  ef.formula as formula,
                  ef.currency.code as currencyCode,
                  ef.customerGroupId.id as customerGroupId,
                  ef.productConditionId as productConditionId
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              JOIN
                  s.elements e
              JOIN
                  e.aptoPriceFormulas ef
              WHERE
                  (p.id.id = :productId AND e.id.id IN (:elementIds) AND ef.currency.code IN (:currencyCodes) AND ef.customerGroupId.id IN (:customerGroupIds))
              ';

        $dqlElementPercentageSurcharges = 'SELECT
                  e.id.id as elementId,
                  e.percentageSurcharge
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              JOIN
                  s.elements e
              WHERE
                  (p.id.id = :productId AND e.id.id IN (:elementIds) AND (e.percentageSurcharge > 0 OR e.percentageSurcharge < 0))
              ';

        $dqlElementDefinitions = 'SELECT
                  e.id.id as elementId,
                  e.definition as definition
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              JOIN
                  s.elements e
              WHERE
                  (p.id.id = :productId AND e.id.id IN (:elementIds))
              ';

        $dqlProductDiscount = 'SELECT
                  p.id.id as productId,
                  pd.name as name,
                  pd.description as description,
                  pd.discount as discount
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.aptoDiscounts pd
              WHERE
                  p.id.id = :productId AND pd.customerGroupId.id = :customerGroupId
              ';

        $dqlSectionDiscount = 'SELECT
                  s.id.id as sectionId,
                  sd.name as name,
                  sd.description as description,
                  sd.discount as discount
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              JOIN
                  s.aptoDiscounts sd
              WHERE
                  p.id.id = :productId AND s.id.id IN (:sectionIds) AND sd.customerGroupId.id = :customerGroupId
              ';

        $dqlElementDiscount = 'SELECT
                  e.id.id as elementId,
                  ed.name as name,
                  ed.description as description,
                  ed.discount as discount
              FROM
                  ' . $this->entityClass . ' p
              JOIN
                  p.sections s
              JOIN
                  s.elements e
              JOIN
                  e.aptoDiscounts ed
              WHERE
                  p.id.id = :productId AND e.id.id IN (:elementIds) AND ed.customerGroupId.id = :customerGroupId
              ';

        $priceModifierQuery = $this->entityManager->createQuery($dqlPriceModifier);
        $productPriceQuery = $this->entityManager->createQuery($dqlProductPrices);
        $sectionPriceQuery = $this->entityManager->createQuery($dqlSectionPrices);
        $elementPriceQuery = $this->entityManager->createQuery($dqlElementPrices);
        $elementPriceMatrixQuery = $this->entityManager->createQuery($dqlElementPriceMatrix);
        $elementPriceFormulaQuery = $this->entityManager->createQuery($dqlElementPriceFormulas);
        $elementPercentageSurchargeQuery = $this->entityManager->createQuery($dqlElementPercentageSurcharges);
        $elementDefinitionsQuery = $this->entityManager->createQuery($dqlElementDefinitions);
        $productDiscountQuery = $this->entityManager->createQuery($dqlProductDiscount);
        $sectionDiscountQuery = $this->entityManager->createQuery($dqlSectionDiscount);
        $elementDiscountQuery = $this->entityManager->createQuery($dqlElementDiscount);

        $priceModifierQuery->setParameters($priceMultiplierParameters);
        $productPriceQuery->setParameters($productPriceParameters);
        $sectionPriceQuery->setParameters($sectionPriceParameters);
        $elementPriceQuery->setParameters($elementPriceParameters);
        $elementPriceMatrixQuery->setParameters($elementPriceMatrixParameters);
        $elementPriceFormulaQuery->setParameters($elementPriceFormulaParameters);
        $elementPercentageSurchargeQuery->setParameters($elementPercentageSurchargeParameters);
        $elementDefinitionsQuery->setParameters($elementDefinitionsParameters);
        $productDiscountQuery->setParameters($productDiscountParameters);
        $sectionDiscountQuery->setParameters($sectionDiscountParameters);
        $elementDiscountQuery->setParameters($elementDiscountParameters);

        /*
        $sectionElementPricePositions = $this->findSectionElementPricePositions($id);

        if (null === $sectionElementPricePositions) {
            $sectionElementPricePositions = [];
        }

        if (!array_key_exists('sections', $sectionElementPricePositions)) {
            $sectionElementPricePositions['sections'] = [];
        }
        */


        $priceModifier = $priceModifierQuery->getScalarResult();
        if (!empty($priceModifier)) {
            $priceModifier = $priceModifier[0]['priceModifier'];
        } else {
            $priceModifier = null;
        }
        return [
            'prices' => [
                'products' => $productPriceQuery->getScalarResult(),
                'sections' => $sectionPriceQuery->getScalarResult(),
                'elements' => $elementPriceQuery->getScalarResult()
            ],
            'priceModifier' => $priceModifier,
            'priceMatrices' => [
                'products' => [],
                'sections' => [],
                'elements' => $elementPriceMatrixQuery->getScalarResult()
            ],
            'priceFormulas' => [
                'products' => [],
                'sections' => [],
                'elements' => $elementPriceFormulaQuery->getScalarResult()
            ],
            'percentageSurcharges' => [
                'products' => [],
                'sections' => [],
                'elements' => $elementPercentageSurchargeQuery->getScalarResult()
            ],
            'discounts' => [
                'products' => $this->decodeJsonProperty($productDiscountQuery->getScalarResult(), 'name'),
                'sections' => $this->decodeJsonProperty($sectionDiscountQuery->getScalarResult(), 'name'),
                'elements' => $this->decodeJsonProperty($elementDiscountQuery->getScalarResult(), 'name')
            ],
            'definitions' => $elementDefinitionsQuery->getScalarResult()
        ];
    }

    /**
     * @param array $list
     * @param string $property
     * @return array
     */
    private function decodeJsonProperty(array $list, string $property): array
    {
        foreach ($list as &$item) {
            if (!array_key_exists($property, $item)) {
                continue;
            }
            $item[$property] = DqlQueryBuilder::decodeJson($item[$property]);
        }
        return $list;
    }

    /**
     * @param string $id
     * @return null
     * @throws DqlBuilderException
     */
    public function findSectionElementPricePositions(string $id)
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
                    'position'
                ],
                'pe' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'position',
                    'definition'
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
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
                ],
                'pe' => [
                    'position' => [DqlQueryBuilder::class, 'decodeInteger']
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
     * @return float
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findTaxRateById(string $id): float
    {
        $dql = 'SELECT
                  p.taxRate
              FROM
                  ' . $this->entityClass . ' p
              WHERE
                  p.id.id = :id
              ';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('id', $id);

        return $query->getSingleScalarResult();
    }

    /**
     * @param string $id
     * @return string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPriceCalculatorIdById(string $id): string
    {
        $dql = 'SELECT
                  p.priceCalculatorId
              FROM
                  ' . $this->entityClass . ' p
              WHERE
                  p.id.id = :id
              ';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('id', $id);

        return $query->getSingleScalarResult();
    }

    /**
     * @return array
     * @throws DqlBuilderException
     */
    public function findTranslatableProductFields(): array
    {
        // get all Products
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'name',
                    'description',
                    'metaTitle',
                    'metaDescription'
                ]
            ])
            ->setPostProcess([
                'p' => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson'],
                    'description' => [DqlQueryBuilder::class, 'decodeJson'],
                    'metaTitle' => [DqlQueryBuilder::class, 'decodeJson'],
                    'metaDescription' => [DqlQueryBuilder::class, 'decodeJson'],
                ]
            ]);

        $results = $builder->getResult($this->entityManager);

        return $results;
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findTranslatableSectionsElements(string $id) {
        $dqlService = new ProductDqlService($this->entityManager, $this->entityClass);
        return $dqlService->findTranslatableSectionsElements($id);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findTranslatableRules(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'pr' => [
                    ['id.id', 'id'],
                    'name',
                    'errorMessage'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['rules', 'pr', 'id']
                ]
            ])
            ->setPostProcess([
                'pr' => [
                    'errorMessage' => [DqlQueryBuilder::class, 'decodeJson'],
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findProductSectionElementPrices(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'ps' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'pe' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'definition'
                ],
                'pp' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ],
                'psp' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ],
                'pep' => [
                    ['id.id', 'id'],
                    ['price.amount', 'amount'],
                    ['price.currency.code', 'currencyCode'],
                    ['customerGroupId.id', 'customerGroupId']
                ]
            ])
            ->setJoins([
                'p' => [
                    ['sections', 'ps', 'id'],
                    ['aptoPrices', 'pp', 'id']
                ],
                'ps' => [
                    ['elements', 'pe', 'id'],
                    ['aptoPrices', 'psp', 'id']
                ],
                'pe' => [
                    ['aptoPrices', 'pep', 'id']
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNextPosition(): int
    {
        $dql = 'SELECT
                  MAX(p.position) as max_position
              FROM
                  ' . $this->entityClass . ' p
              ';

        $query = $this->entityManager->createQuery($dql);
        $maxPosition = $query->getSingleScalarResult();

        if (!($maxPosition >= 0)) {
            return 0;
        }
        else {
            $newPosition = round($maxPosition/10) * 10;
            if ($newPosition <= $maxPosition) {
                return (int) $newPosition + 10;
            }
            return (int) $newPosition;
        }
    }
}
