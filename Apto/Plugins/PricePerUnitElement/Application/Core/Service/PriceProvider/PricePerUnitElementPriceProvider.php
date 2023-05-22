<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Core\Service\PriceProvider;

use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\Product\Element\PricePerUnitElementDefinition;
use Money\Money;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Service\Math\Calculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\AdditionalPriceInformationProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ElementPriceProvider;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\PricePerUnitElement\Application\Core\Query\PricePerUnitItem\PricePerUnitItemFinder;
use Apto\Catalog\Application\Core\Query\Product\Element\ProductElementFinder;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;

class PricePerUnitElementPriceProvider implements ElementPriceProvider, AdditionalPriceInformationProvider
{
    /**
     * @var PricePerUnitItemFinder
     */
    protected $pricePerUnitItemFinder;

    /**
     * @var ProductElementFinder
     */
    protected $productElementFinder;

    /**
     * @var AptoJsonSerializer
     */
    protected $aptoJsonSerializer;

    /**
     * @var Calculator
     */
    protected $calculator;

    /**
     * @param PricePerUnitItemFinder $pricePerUnitItemFinder
     * @param ProductElementFinder $productElementFinder
     * @param AptoJsonSerializer $aptoJsonSerializer
     */
    public function __construct(
        PricePerUnitItemFinder $pricePerUnitItemFinder,
        ProductElementFinder $productElementFinder,
        AptoJsonSerializer $aptoJsonSerializer
    ) {
        $this->pricePerUnitItemFinder = $pricePerUnitItemFinder;
        $this->productElementFinder = $productElementFinder;
        $this->aptoJsonSerializer = $aptoJsonSerializer;
        $this->calculator = new Calculator();
    }

    /**
     * @return string
     */
    public function getElementDefinitionClass(): string
    {
        return PricePerUnitElementDefinition::class;
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @param Money $elementPrice
     * @param Money $basePrice
     * @return Money
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    public function getPrice(
        PriceCalculator $priceCalculator,
        AptoUuid $sectionId,
        AptoUuid $elementId,
        ElementDefinition $elementDefinition,
        Money $elementPrice,
        Money $basePrice
    ): Money {
        // set price calculator values
        $state = $priceCalculator->getState();
        $customerGroupId = $priceCalculator->getCustomerGroup()['id'];
        $fallbackCustomerGroup = $priceCalculator->getFallbackCustomerGroupOrNull();
        $fallbackCustomerGroupId = null !== $fallbackCustomerGroup ? $fallbackCustomerGroup['id'] : null;

        // get static values from element definition
        $staticValues = $elementDefinition->getStaticValues();

        //@deprecated get value from defined property
        $value = null;
        if (
            null !== $staticValues['sectionId'] &&
            null !== $staticValues['elementId'] &&
            null !== $staticValues['selectableValue'] &&
            null !== $staticValues['selectableValueType']
        ) {
            $valueType = $this->getSelectableValueType($staticValues);
            $value = $this->getReferenceValue(
                $state,
                $staticValues['sectionId'],
                $staticValues['elementId'],
                $staticValues['selectableValue'],
                $valueType
            );
        }

        // use new elementValueRef list
        if (isset($staticValues['elementValueRefs'])) {
            foreach ($staticValues['elementValueRefs'] as $elementValueRef) {
                $valueType = $this->getSelectableValueType($elementValueRef);

                // try get value
                $value = $this->getReferenceValue(
                    $state,
                    $elementValueRef['sectionId'],
                    $elementValueRef['elementId'],
                    $elementValueRef['selectableValue'],
                    $valueType
                );

                // if value found set value to use for price calculation
                if (null !== $value) {
                    break;
                }
            }
        }
        $conversionFactor = $staticValues['conversionFactor'];

        // skip, if value is not set
        if ($value === null) {
            if (isset($staticValues['minOne']) && $staticValues['minOne']) {
                $value = $this->calculator->div('1', $conversionFactor);
            } else {
                return $elementPrice;
            }
        }

        if ((isset($staticValues['minOne']) && $staticValues['minOne'] && $this->calculator->mul($conversionFactor, $value) < 1)) {
            $value = $this->calculator->div('1', $conversionFactor);
        }

        // increases the value to 1 if the value is smaller and it is desired.

        if (isset($staticValues['minOne']) && $staticValues['minOne']) {
            $value = $value < 1 ? 1 : $value;
        }

        // get price data
        $prices = $this->pricePerUnitItemFinder->findPrice(
            $elementId->getId(),
            $customerGroupId,
            $fallbackCustomerGroupId,
            $elementPrice->getCurrency()->getCode(),
            $priceCalculator->getFallbackCurrency()->getCode()
        );

        // add preferred price multiplied with conversion factor and value
        $preferredPrice = $priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($prices);

        // Set price to factor 1, if needed & desired.
        if (isset($staticValues['minOne']) && $staticValues['minOne'] && $this->calculator->mul($conversionFactor, $value) < 1) {
            return $elementPrice->add($preferredPrice);
        }

        return $elementPrice->add($preferredPrice->multiply($this->calculator->mul($conversionFactor, $value)));
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @param Money $elementPrice
     * @param Money $basePrice
     * @return array
     */
    public function getAdditionalInformation(
        PriceCalculator $priceCalculator,
        AptoUuid $sectionId,
        AptoUuid $elementId,
        ElementDefinition $elementDefinition,
        Money $elementPrice,
        Money $basePrice
    ): array {
        $customerGroupId = $priceCalculator->getCustomerGroup()['id'];
        $fallbackCustomerGroup = $priceCalculator->getFallbackCustomerGroupOrNull();
        $fallbackCustomerGroupId = null !== $fallbackCustomerGroup ? $fallbackCustomerGroup['id'] : null;

        // get price data
        $prices = $this->pricePerUnitItemFinder->findPrice(
            $elementId->getId(),
            $customerGroupId,
            $fallbackCustomerGroupId,
            $elementPrice->getCurrency()->getCode(),
            $priceCalculator->getFallbackCurrency()->getCode()
        );

        return [
            'formatPricePaths' => [
                [
                    'apto-plugin-live-price',
                    'price'
                ]
            ],
            'apto-plugin-live-price' => [
                'price' => $priceCalculator->getTaxCalculator()->getDisplayPrice($priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($prices))
            ]
        ];
    }

    /**
     * @param State $state
     * @param string $sectionId
     * @param string $elementId
     * @param string $selectableValue
     * @param string $selectableValueType
     * @return float|null
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    private function getReferenceValue(
        State $state,
        string $sectionId,
        string $elementId,
        string $selectableValue,
        string $selectableValueType
    ) {
        switch ($selectableValueType) {
            case 'Selectable':
            {
                $value = $state->getValue(
                    new AptoUuid($sectionId),
                    new AptoUuid($elementId),
                    $selectableValue
                );

                return $value === null ? null : (float)$value;
            }
            case 'Computable':
            {
                return $this->getComputableValue($state, $sectionId, $elementId, $selectableValue);
            }
            default:
            {
                return null;
            }
        }
    }

    /**
     * @param State $state
     * @param string $sectionId
     * @param string $elementId
     * @param string $selectableValue
     * @return float|null
     * @throws AptoJsonSerializerException
     */
    private function getComputableValue(State $state, string $sectionId, string $elementId, string $selectableValue)
    {
        $stateArray = $state->getState();

        if (
            !array_key_exists($sectionId, $stateArray) ||
            !array_key_exists($elementId, $stateArray[$sectionId]) ||
            !is_array($stateArray[$sectionId][$elementId])
        ) {
            return null;
        }

        $product = $this->productElementFinder->findById($elementId);
        /** @var ElementDefinition $elementDefinition */
        $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize(json_encode($product['definition']));
        $computableValues = $elementDefinition->getComputableValues($stateArray[$sectionId][$elementId]);

        if (!array_key_exists($selectableValue, $computableValues)) {
            return null;
        }

        return (float)$computableValues[$selectableValue];
    }

    /**
     * @param array $options
     * @return string
     */
    private function getSelectableValueType(array $options): string
    {
        if (array_key_exists('selectableValueType', $options)) {
            return $options['selectableValueType'];
        }
        return 'Selectable';
    }
}
