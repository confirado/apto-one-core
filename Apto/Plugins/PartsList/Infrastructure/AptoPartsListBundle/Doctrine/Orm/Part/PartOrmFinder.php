<?php

namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\Doctrine\Orm\Part;

use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlPaginatorBuilder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlQueryBuilder;
use Apto\Catalog\Application\Core\Query\Product\Element\ProductElementFinder;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\PartsList\Application\Core\Query\Part\PartFinder;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\QuantityCalculation;
use Doctrine\ORM\EntityManagerInterface;

class PartOrmFinder extends AptoOrmFinder implements PartFinder
{
    const ENTITY_CLASS = Part::class;

    const MODEL_VALUES = [
        ['id.id', 'id'],
        'active',
        'partNumber',
        'name',
        'description',
        'baseQuantity'
    ];

    const MODEL_POST_PROCESSES = [
        'active' => [DqlQueryBuilder::class, 'decodeBool'],
        'name' => [DqlQueryBuilder::class, 'decodeJson'],
        'description' => [DqlQueryBuilder::class, 'decodeJson'],
        'amount' => [DqlQueryBuilder::class, 'decodeInteger'],
        'baseQuantity' => [DqlQueryBuilder::class, 'decodeInteger']
    ];

    /**
     * @var string
     */
    private $productEntityClass;

    /**
     * @var string
     */
    private $sectionEntityClass;

    /**
     * @var string
     */
    private $elementEntityClass;

    /**
     * @var string
     */
    private $elementUsageEntityClass;

    /**
     * @var string
     */
    private $ruleUsageEntityClass;

    /**
     * @var ProductElementFinder
     */
    private $productElementFinder;

    /**
     * @var AptoJsonSerializer
     */
    protected $aptoJsonSerializer;

    /**
     * PartOrmFinder constructor.
     * @param EntityManagerInterface $entityManager
     * @param ProductElementFinder $productElementFinder
     * @param AptoJsonSerializer $aptoJsonSerializer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProductElementFinder $productElementFinder,
        AptoJsonSerializer $aptoJsonSerializer
    ) {
        parent::__construct($entityManager);
        $this->productEntityClass = 'Apto\Catalog\Domain\Core\Model\Product\Product';
        $this->sectionEntityClass = 'Apto\Catalog\Domain\Core\Model\Product\Section\Section';
        $this->elementEntityClass = 'Apto\Catalog\Domain\Core\Model\Product\Element\Element';
        $this->elementUsageEntityClass = 'Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\ElementUsage';
        $this->ruleUsageEntityClass = 'Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\RuleUsage';
        $this->productElementFinder = $productElementFinder;
        $this->aptoJsonSerializer = $aptoJsonSerializer;
    }

    /**
     * @param string $id
     * @return array|null
     * @throws DqlBuilderException
     */
    public function findById(string $id): ?array
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => self::MODEL_VALUES,
                'u' => [
                    ['id.id', 'id'],
                    'unit'
                ],
                'a' => [
                    ['id.id', 'id']
                ],
                'ap'=> [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'c' => [
                    ['id.id', 'id'],
                ],
            ])
            ->setJoins([
                'p' => [
                    ['unit', 'u', 'id'],
                    ['associatedProducts', 'a', 'id'],
                    ['category', 'c', 'id']
                ],
                'a' => [
                    ['product', 'ap', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => self::MODEL_POST_PROCESSES
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return null;
        }

        // set unit
        if (array_key_exists(0, $result['unit']) && count($result['unit'][0]) > 0) {
            $result['unit'] = $result['unit'][0];
        } else {
            $result['unit'] = null;
        }

        $result['category'] = empty($result['category']) ? null : $result['category'][0]['id'];

        return $result;
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
    public function findByListingPageNumber(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setPage($pageNumber)
            ->setRecordsPerPage($recordsPerPage)
            ->setValues([
                'p' => self::MODEL_VALUES,
                'a' => [
                    ['id.id', 'id']
                ],
                'ap'=> [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier']
                ],
                'c' => [
                    ['id.id', 'id'],
                ],
            ])
            ->setJoins([
               'p' => [
                   ['associatedProducts', 'a', 'id'],
                   ['category', 'c', 'id']
               ],
               'a' => [
                   ['product', 'ap', 'id']
               ]
           ])
            ->setSearch([
                'p' => [
                    'id.id',
                    'partNumber',
                    'name'
                ],
                'ap' => [
                    'identifier.value'
                ]
            ], $searchString)
            ->setPostProcess([
                'p' => self::MODEL_POST_PROCESSES
            ])
            ->setOrderBy([
                ['p.created', 'DESC']
            ]);

        $result = $builder->getResult($this->entityManager);
        foreach ($result['data'] as &$data) {
            $data['products'] = [];
               foreach ($data['associatedProducts'] as $associatedProduct) {
                   $data['products'][] = $associatedProduct['product'][0]['identifier'];
               }
            $data['products'] = implode(', ', $data['products']);
            $data['category'] = empty($data['category']) ? null : $data['category'][0]['id'];
        }

        return $result;
    }

    /**
     * @param string $id
     * @return array|null
     * @throws AptoJsonSerializerException
     * @throws DqlBuilderException
     */
    public function findElementUsageById(string $id): ?array
    {
        $builder = new DqlQueryBuilder($this->elementUsageEntityClass);
        $builder
            ->findById($id)
            ->setValues([
                'e' => [
                    ['id.id', 'id'],
                    ['usageForUuid.id', 'usageForUuid'],
                    ['quantity.quantity', 'quantity'],
                    ['quantityCalculation.active', 'quantityCalculationActive'],
                    ['quantityCalculation.operation', 'quantityCalculationOperation'],
                    ['quantityCalculation.fieldType', 'quantityCalculationFieldType'],
                    ['quantityCalculation.field', 'quantityCalculationField'],
                    ['quantityCalculation.fieldPosition', 'quantityCalculationFieldPosition']
                ]
            ])
            ->setPostProcess([
                'e' => [
                    'quantityCalculationActive' => [DqlQueryBuilder::class, 'decodeBool']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return null;
        }

        $result['element'] = $this->getElementUsageElement($result['usageForUuid']);
        $result['fieldTypes'] = QuantityCalculation::FIELD_TYPES;
        $result['operations'] = QuantityCalculation::OPERATIONS;
        $result['fieldPositions'] = QuantityCalculation::FIELD_POSITIONS;

        $result['quantityCalculation'] = [
            'active' => $result['quantityCalculationActive'],
            'operation' => $result['quantityCalculationOperation'],
            'fieldType' => $result['quantityCalculationFieldType'],
            'field' => $result['quantityCalculationField'],
            'fieldPosition' => $result['quantityCalculationFieldPosition']
        ];

        unset(
            $result['quantityCalculationActive'],
            $result['quantityCalculationOperation'],
            $result['quantityCalculationFieldType'],
            $result['quantityCalculationField'],
            $result['quantityCalculationFieldPosition']
        );

        return $result;
    }

    /**
     * @param string $id
     * @return array|null
     * @throws AptoJsonSerializerException
     * @throws DqlBuilderException
     */
    public function findRuleUsageById(string $id): ?array
    {
        $builder = new DqlQueryBuilder($this->ruleUsageEntityClass);
        $builder
            ->findById($id)
            ->setValues([
                'r' => [
                    'active',
                    ['id.id', 'id'],
                    'name',
                    ['quantity.quantity', 'quantity'],
                    'conditionsOperator'

                ],
                'c' => [
                    ['id.id', 'id'],
                    ['productId.id', 'productId'],
                    'sectionId',
                    'elementId',
                    'computedValueId',
                    'property',
                    ['operator.operator', 'operator'],
                    'value'
                ]
            ])
            ->setJoins([
                'r' => [
                    ['conditions', 'c', 'id']
                ]
            ])
            ->setPostProcess([
                'r' => [
                    'active' => [DqlQueryBuilder::class, 'decodeBool'],
                    'conditionsOperator' => [DqlQueryBuilder::class, 'decodeInteger'],
                    'name' => [DqlQueryBuilder::class, 'castString'],
                    'sectionId' => [DqlQueryBuilder::class, 'castString'],
                    'elementId' => [DqlQueryBuilder::class, 'castString'],
                    'computedValueId' => [DqlQueryBuilder::class, 'castString'],
                ],
                'c' => [
                    'value' => [DqlQueryBuilder::class, 'castString']
                ]
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return null;
        }
        return $result;
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findProductUsages(string $id): array
    {
        return $this->findUsages($id, 'productUsages');
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findSectionUsages(string $id): array
    {
        return $this->findUsages($id, 'sectionUsages');
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findElementUsages(string $id): array
    {
        return $this->findUsages($id, 'elementUsages');
    }

    /**
     * @param string $id
     * @return array
     * @throws DqlBuilderException
     */
    public function findRuleUsages(string $id): array
    {
        return $this->findUsages($id, 'ruleUsages');
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findProducts(string $searchString): array
    {
        return $this->findProductsSectionsOrElements(
            $this->productEntityClass, 'p', $searchString
        );
    }

    /**
     * @return array
     * @throws AptoJsonSerializerException
     * @throws DqlBuilderException
     */
    public function findProductsSectionsElements(): array
    {
        $builder = new DqlQueryBuilder($this->productEntityClass);
        $builder
            ->setValues([
                'p' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                ],
                'ps' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                ],
                'pc' => [
                    ['id.id', 'id'],
                    'name'
                ],
                'pse' => [
                    ['id.id', 'id'],
                    ['identifier.value', 'identifier'],
                    'definition'
                ]
            ])
            ->setJoins([
                'p' => [
                    ['sections', 'ps', 'id'],
                    ['computedProductValues', 'pc', 'id']
                ],
                'ps' => [
                    ['elements', 'pse', 'id']
                ]
            ])
            ->setOrderBy([
                ['p.identifier.value', 'ASC'],
                ['ps.identifier.value', 'ASC'],
                ['pse.identifier.value', 'ASC'],
            ]);
        $result = $builder->getResult($this->entityManager);

        foreach ($result['data'] as &$product) {
            foreach ($product['sections'] as $iSection => &$section) {
                if (isset($section['elements'])) {
                    foreach ($section['elements'] as $iElement => &$element) {
                        // set element definition
                        /** @var ElementDefinition $elementDefinition */
                        $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize($element['definition']);

                        $definition = [];
                        $definition['name'] = $elementDefinition::getName();
                        $definition['component'] = $elementDefinition::getFrontendComponent();

                        $selectableValues = $elementDefinition->getSelectableValues();
                        /** @var ElementValueCollection $selectableValue */
                        foreach ($selectableValues as $selectableProperty => $selectableValue) {
                            $definition['properties'][$selectableProperty] = $selectableValue->jsonSerialize();
                        }

                        $element['definition'] = $definition;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findSections(string $searchString): array
    {
        return $this->findProductsSectionsOrElements(
            $this->sectionEntityClass, 's', $searchString
        );
    }

    /**
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    public function findElements(string $searchString): array
    {
        return $this->findProductsSectionsOrElements(
            $this->elementEntityClass, 'e', $searchString
        );
    }

    /**
     * @param string $id
     * @return array
     * @throws \Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\DqlBuilderException
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
                    ['customerGroupId.id', 'customerGroupId']
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
     * @param string $entityClass
     * @param string $entityAlias
     * @param string $searchString
     * @return array
     * @throws DqlBuilderException
     */
    private function findProductsSectionsOrElements(string $entityClass, string $entityAlias, string $searchString): array
    {
        $builder = new DqlQueryBuilder($entityClass);
        $builder
            ->setPostProcess([
                $entityAlias => [
                    'name' => [DqlQueryBuilder::class, 'decodeJson']
                ]
            ]);

        switch ($entityAlias) {
            case 's': {
                $builder->setValues([
                    $entityAlias => [
                        ['id.id', 'id'],
                        ['identifier.value', 'identifier'],
                        'name'
                    ],
                    'p' => [
                        ['id.id', 'id'],
                        ['identifier.value', 'identifier'],
                    ]
                ])
                ->setJoins([
                    $entityAlias => [
                        ['product', 'p', 'id']
                    ]
                ])
                ->setOrderBy([
                    ['p.identifier.value', 'ASC'],
                    [$entityAlias . '.identifier.value', 'ASC']
                ]);
                break;
            }
            case 'e': {
                $builder->setValues([
                    $entityAlias => [
                        ['id.id', 'id'],
                        ['identifier.value', 'identifier'],
                        'name'
                    ],
                    'p' => [
                        ['id.id', 'id'],
                        ['identifier.value', 'identifier'],
                    ],
                    's' => [
                        ['id.id', 'id'],
                        ['identifier.value', 'identifier'],
                    ]
                ])
                ->setJoins([
                    $entityAlias => [
                        ['section', 's', 'id']
                    ],
                    's' => [
                        ['product', 'p', 'id']
                    ]
                ])
                ->setOrderBy([
                    ['p.identifier.value', 'ASC'],
                    ['s.identifier.value', 'ASC'],
                    [$entityAlias . '.identifier.value', 'ASC']
                ]);
                break;
            }
            default: {
                $builder->setValues([
                    $entityAlias => [
                        ['id.id', 'id'],
                        ['identifier.value', 'identifier'],
                        'name'
                    ]
                ])
                ->setOrderBy([
                    [$entityAlias . '.identifier.value', 'ASC']
                ]);
                break;
            }
        }

        if ($searchString) {
            $builder->setSearch([
                $entityAlias => [
                    'id.id',
                    'identifier.value',
                    'name'
                ]
            ], $searchString);
        }

        $result = $builder->getResult($this->entityManager);

        switch ($entityAlias) {
            case 's': {
                foreach ($result['data'] as &$value) {
                    $value['identifierSingle'] = $value['identifier'];
                    $value['identifier'] = $value['product'][0]['identifier'] . ' | ' . $value['identifier'];
                    $value['productId'] = $value['product'][0]['id'];
                    unset($value['product']);
                }
                break;
            }
            case 'e': {
                foreach ($result['data'] as &$value) {
                    $value['identifierSingle'] = $value['identifier'];
                    $value['identifier'] = $value['section'][0]['product'][0]['identifier'] . ' | ' . $value['section'][0]['identifier'] . ' | ' . $value['identifier'];
                    $value['productId'] = $value['section'][0]['product'][0]['id'];
                    unset($value['section']);
                }
                break;
            }
        }

        return $result;
    }

    /**
     * @param string $id
     * @param string $usages
     * @return array
     * @throws DqlBuilderException
     */
    private function findUsages(string $id, string $usages): array
    {
        $usageValues = [
            ['id.id', 'id'],
            ['usageForUuid.id', 'usageForUuid'],
            ['quantity.quantity', 'quantity']
        ];

        if ($usages === 'ruleUsages') {
            $usageValues = [
                ['id.id', 'id'],
                'name',
                ['quantity.quantity', 'quantity']
            ];
        }
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'p' => [
                ],
                'u' => $usageValues
            ])
            ->setJoins([
                'p' => [
                    [$usages, 'u', 'id']
                ]
            ])
            ->setPostProcess([
                'p' => self::MODEL_POST_PROCESSES
            ]);

        $result = $builder->getSingleResultOrNull($this->entityManager);

        if (null === $result) {
            return [];
        }
        return $result[$usages];
    }

    /**
     * @param string $elementId
     * @return array
     * @throws AptoJsonSerializerException
     */
    private function getElementUsageElement(string $elementId): array
    {
        $element = $this->productElementFinder->findById($elementId);

        /** @var ElementDefinition $elementDefinition */
        $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize(json_encode($element['definition']));

        // get element definition selectable values
        $selectedValues = [];
        $selectableValues = $elementDefinition->getSelectableValues();
        $element['selectableValues'] = [];

        /**
         * @var string $property
         * @var ElementValueCollection $selectableValue
         */
        foreach ($selectableValues as $property => $selectableValue) {
            $element['selectableValues'][] = $property;
            $selectedValues[$property] = $selectableValue->getAnyValue();
        }

        // get element definition computable values
        $element['computableValues'] = array_keys($elementDefinition->getComputableValues($selectedValues));

        // unset element definition
        unset($element['definition']);

        // return element
        return $element;
    }
}
