<?php

namespace Apto\Catalog\Domain\Core\Service\StateValidation;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Query\Product\Condition\ProductConditionSetFinder;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Domain\Core\Factory\ConfigurableProduct\ConfigurableProduct;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Condition;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\LinkOperator;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\Rule\Payload\RulePayloadFactory;
use Apto\Catalog\Domain\Core\Factory\RuleFactory\RuleFactory;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool\PoolFinder;

class ValueValidationService
{
    /**
     * @var PoolFinder
     */
    protected $poolFinder;

    /**
     * @var ProductConditionSetFinder
     */
    private ProductConditionSetFinder $productConditionSetFinder;

    /**
     * @var RulePayloadFactory
     */
    protected RulePayloadFactory $rulePayloadFactory;

    /**
     * @param PoolFinder $poolFinder
     * @param ProductConditionSetFinder $productConditionSetFinder
     * @param RulePayloadFactory $rulePayloadFactory
     */
    public function __construct(PoolFinder $poolFinder, ProductConditionSetFinder $productConditionSetFinder, RulePayloadFactory $rulePayloadFactory)
    {
        $this->poolFinder = $poolFinder;
        $this->productConditionSetFinder = $productConditionSetFinder;
        $this->rulePayloadFactory = $rulePayloadFactory;
    }

    private function getSectionListGroupedBy(State $state, string $fieldName): array
    {
        $sectionList = [];
        foreach ($state->getStateWithoutParameters() as $stateItem) {
            if (!isset($sectionList[$stateItem['sectionId'].$stateItem[$fieldName]] )) {
                $sectionList[$stateItem['sectionId'].$stateItem[$fieldName]] = [];
            }

            $sectionList[$stateItem['sectionId'].$stateItem[$fieldName]][] = $stateItem;
        }
        return array_values($sectionList);
    }

    /**
     *  we expect array of array parameters:
     *  [
     *     [
     *        'name' => 'quantity',
     *        'value' => 1
     *     ],
     *     [
     *        'name' => 'repetitions',
     *        'value' => 1
     *     ],
     *  ];
     *
     * @param array $parameters
     *
     * @return void
     */
    public function assertValidParameters(array $parameters): void
    {
        foreach ($parameters as $parameter) {
            if(!array_key_exists('name', $parameter) || !array_key_exists('value', $parameter)) {
                throw new \InvalidArgumentException('Parameters must always have name and value properties!');
            }
        }
    }

    /**
     * Checks element value validity (values property in state)
     *
     * @param ConfigurableProduct $product
     * @param State               $state
     *
     * @return void
     * @throws InvalidUuidException
     * @throws CircularReferenceException
     */
    public function assertValidValues(ConfigurableProduct $product, State $state): void
    {
        foreach ($this->getSectionListGroupedBy($state, 'repetition') as $section) {
            $sectionId = $section[0]['sectionId'];
            $sectionUuid = new AptoUuid($sectionId);

            self::assertHasSection($product, $sectionUuid);

            // if multiple elements are NOT allowed in the section, but we have multiple in current section
            if (!$product->isSectionMultiple($sectionUuid) && count($section) > 1) {
                throw new InvalidStateException(
                    sprintf(
                        'The given section \'%s(%s)\' does not allow multiple elements in product \'%s(%s)\'.',
                        $sectionId,
                        $product->getSectionIdentifier($sectionUuid),
                        $product->getId()->getId(),
                        $product->getIdentifier()
                    ),
                    $product->getId()->getId(),
                    $sectionId
                );
            }

            foreach ($section as $element) {
                $elementId = $element['elementId'];
                $elementUuid = new AptoUuid($elementId);
                self::assertHasElement($product, $sectionUuid, $elementUuid);

                // skip elements without properties
                if (!empty($element['values'])) {
                    foreach ($element['values'] as $property => $value) {
                        self::assertHasProperty($product, $sectionUuid, $elementUuid, $property);
                        self::assertHasValue($product, $sectionUuid, $elementUuid, $property, $value);
                    }
                }
            }
        }

        // filter out materials by its conditions
        $this->filterOutMaterialsByCondition($product, $state);
    }

    /**
     * @param ConfigurableProduct $product
     * @param State $state
     * @return void
     * @throws InvalidUuidException
     * @throws CircularReferenceException
     */
    private function filterOutMaterialsByCondition(ConfigurableProduct $product, State $state)
    {
        $rulePayload = $this->rulePayloadFactory->getPayload($product, $state);
        foreach ($state->getStateWithoutParameters() as $element) {
            if (!(array_key_exists('aptoElementDefinitionId', $element['values']) && $element['values']['aptoElementDefinitionId'] === 'apto-element-material-picker')) {
                continue;
            }

            $poolId = $element['values']['poolId'];

            $items = $this->poolFinder->findPoolItemsFiltered($poolId, ['colorRating' => null, 'priceGroup' => null, 'properties' => []]);
            $itemsMatchingCondition = [];

            foreach ($items['data'] as $item) {

                if ($item['material']['conditionSets'] && !empty($item['material']['conditionSets'])) {

                    $productConditionsResult = $this->productConditionSetFinder->findByIdsForProduct($product->getId()->getId(), $item['material']['conditionSets']);

                    $counter = 0;
                    foreach ($productConditionsResult['data'] as $key => $productCondition) {

                        $criterion = new Condition(
                            new LinkOperator($productCondition['conditionsOperator']),
                            RuleFactory::criteriaFromArray($productCondition['conditions'])
                        );

                        if ($criterion->isFulfilled($state, $rulePayload)) {
                            $counter++;
                        }
                    }

                    if ($counter >= count($productConditionsResult['data'])) {
                        $itemsMatchingCondition[] = $item['material']['id'];
                    }

                } else {
                    $itemsMatchingCondition[] = $item['material']['id'];
                }
            }

            foreach ($this->getUsedMaterialIds($element['values']) as $materialId) {
                if (!in_array($materialId, $itemsMatchingCondition)) {
                    $state->removeElement(
                        new AptoUuid($element['sectionId']),
                        new AptoUuid($element['elementId']),
                        $element['repetition']
                    );
                    break;
                }
            }
        }
    }

    /**
     * @param array $elementValues
     * @return array
     */
    private function getUsedMaterialIds(array $elementValues): array
    {
        $materialIds = [];

        if ($elementValues['materialId']) {
            $materialIds[] = $elementValues['materialId'];
        }

        if ($elementValues['materialIdSecondary']) {
            $materialIds[] = $elementValues['materialIdSecondary'];
        }

        if (array_key_exists('materials', $elementValues)) {
            foreach ($elementValues['materials'] as $material) {
                $materialIds[] = $material['id'];
            }
        }

        if (array_key_exists('materialsSecondary', $elementValues)) {
            foreach ($elementValues['materialsSecondary'] as $materialSecondary) {
                $materialIds[] = $materialSecondary['id'];
            }
        }

        return $materialIds;
    }

    /**
     * Throw exception if given section is not contained in product
     * @param ConfigurableProduct $product
     * @param AptoUuid $sectionId
     * @throws InvalidUuidException
     */
    public function assertHasSection(ConfigurableProduct $product, AptoUuid $sectionId): void
    {
        if (!$product->hasSection($sectionId)) {
            throw new InvalidStateException(
                sprintf(
                    'The given section \'%s(%s)\' does not exist in product \'%s(%s)\'.',
                    $sectionId->getId(),
                    $product->getSectionIdentifier($sectionId),
                    $product->getId()->getId(),
                    $product->getIdentifier()
                ),
                $product->getId()->getId(),
                $sectionId->getId()
            );
        }
    }

    /**
     * Throw exception if given element is not contained in product
     * @param ConfigurableProduct $product
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @throws InvalidUuidException
     */
    public function assertHasElement(ConfigurableProduct $product, AptoUuid $sectionId, AptoUuid $elementId): void
    {
        if (!$product->hasElement($sectionId, $elementId)) {
            throw new InvalidStateException(
                sprintf(
                    'The given element \'%s(%s)\' in section \'%s(%s)\' does not exist in product \'%s(%s)\'.',
                    $elementId->getId(),
                    $product->getElementIdentifier($sectionId, $elementId),
                    $sectionId->getId(),
                    $product->getSectionIdentifier($sectionId),
                    $product->getId()->getId(),
                    $product->getIdentifier()
                ),
                $product->getId()->getId(),
                $sectionId->getId(),
                $elementId->getId()
            );
        }
    }

    /**
     * Throw exception if given property is not contained in product
     * @param ConfigurableProduct $product
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @throws InvalidUuidException
     */
    public function assertHasProperty(ConfigurableProduct $product, AptoUuid $sectionId, AptoUuid $elementId, string $property): void
    {
        if (!$product->hasProperty($sectionId, $elementId, $property)) {
            throw new InvalidStateException(
                sprintf(
                    'The given property \'%s\' in element \'%s(%s)\' and section \'%s(%s)\' does not exist in product \'%s(%s)\'.',
                    $property,
                    $elementId->getId(),
                    $product->getElementIdentifier($sectionId, $elementId),
                    $sectionId->getId(),
                    $product->getSectionIdentifier($sectionId),
                    $product->getId()->getId(),
                    $product->getIdentifier()
                ),
                $product->getId()->getId(),
                $sectionId->getId(),
                $elementId->getId(),
                $property
            );
        }
    }

    /**
     * @param ConfigurableProduct $product
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param string $property
     * @param $value
     * @throws InvalidUuidException
     */
    public function assertHasValue(ConfigurableProduct $product, AptoUuid $sectionId, AptoUuid $elementId, string $property, $value): void
    {
        if (!$product->hasValue($sectionId, $elementId, $property, $value)) {
            throw new InvalidStateException(
                sprintf(
                    'The given value \'%s\' is not allowed for property \'%s\' in element \'%s(%s)\' and section \'%s(%s)\'.',
                    print_r($value, true),
                    $property,
                    $elementId->getId(),
                    $product->getElementIdentifier($sectionId, $elementId),
                    $sectionId->getId(),
                    $product->getSectionIdentifier($sectionId)
                ),
                $product->getId()->getId(),
                $sectionId->getId(),
                $elementId->getId(),
                $property,
                $value,
                $product->getElementErrorMessage($sectionId, $elementId)
            );
        }
    }
}
