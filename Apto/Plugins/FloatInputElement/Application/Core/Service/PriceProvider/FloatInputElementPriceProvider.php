<?php

namespace Apto\Plugins\FloatInputElement\Application\Core\Service\PriceProvider;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Service\Math\Calculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\AdditionalPriceInformationProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ElementPriceProvider;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\FloatInputElement\Application\Core\Query\FloatInputItem\FloatInputItemFinder;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\Product\Element\FloatInputElementDefinition;
use Money\Money;

class FloatInputElementPriceProvider implements ElementPriceProvider, AdditionalPriceInformationProvider
{
    /**
     * @var FloatInputItemFinder
     */
    protected $floatInputItemFinder;

    /**
     * @var Calculator
     */
    protected $calculator;

    /**
     * FloatInputElementPriceProvider constructor.
     * @param FloatInputItemFinder $floatInputItemFinder
     */
    public function __construct(FloatInputItemFinder $floatInputItemFinder)
    {
        $this->floatInputItemFinder = $floatInputItemFinder;
        $this->calculator = new Calculator();
    }

    /**
     * @return string
     */
    public function getElementDefinitionClass(): string
    {
        return FloatInputElementDefinition::class;
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @param Money $elementPrice
     * @param Money $basePrice
     * @return Money
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

        // get value from state
        $value = (float)$state->getValue($sectionId, $elementId, 'value');

        // skip, if value is not set
        if ($value == 0) {
            return $elementPrice;
        }

        // get price data
        $prices = $this->floatInputItemFinder->findPrice(
            $elementId->getId(),
            $customerGroupId,
            $fallbackCustomerGroupId,
            $elementPrice->getCurrency()->getCode(),
            $priceCalculator->getFallbackCurrency()->getCode()
        );

        // add preferred price
        $conversionFactor = array_key_exists('conversionFactor', $staticValues) ? $staticValues['conversionFactor'] : '1';
        $preferredPrice = $priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($prices);
        return $elementPrice->add($preferredPrice->multiply($this->calculator->mul($conversionFactor, (string) $value)));
    }

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
        $prices = $this->floatInputItemFinder->findPrice(
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
                'price' => $priceCalculator->getTaxCalculator()->getDisplayPrice(
                    $priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($prices)
                )
            ]
        ];
    }
}
