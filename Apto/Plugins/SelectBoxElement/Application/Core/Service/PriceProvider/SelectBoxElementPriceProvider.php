<?php

namespace Apto\Plugins\SelectBoxElement\Application\Core\Service\PriceProvider;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\AdditionalPriceInformationProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ElementPriceProvider;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\SelectBoxElement\Application\Core\Query\SelectBoxItem\SelectBoxItemFinder;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\Product\Element\SelectBoxElementDefinition;
use Money\Money;

class SelectBoxElementPriceProvider implements ElementPriceProvider, AdditionalPriceInformationProvider
{
    /**
     * @var SelectBoxItemFinder
     */
    protected $selectBoxItemFinder;

    /**
     * SelectBoxElementPriceProvider constructor.
     * @param $selectBoxItemFinder
     */
    public function __construct(SelectBoxItemFinder $selectBoxItemFinder)
    {
        $this->selectBoxItemFinder = $selectBoxItemFinder;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClass(): string
    {
        return SelectBoxElementDefinition::class;
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
        $boxes = $state->getValue($sectionId, $elementId, 'boxes');

        // @todo boxes should always be of type array or null, an empty array or null if no selection is done, sometimes it seems to be an empty string
        if (!is_array($boxes)) {
            $boxes = [];
        }

        foreach ($boxes as $box) {
            $id = $box['id'];
            $multi = floatval(str_replace(',', '.', $box['multi']));
            $prices = $this->selectBoxItemFinder->findPrice(
                $id,
                $customerGroupId,
                $fallbackCustomerGroupId,
                $elementPrice->getCurrency()->getCode(),
                $priceCalculator->getFallbackCurrency()->getCode()
            );
            $elementPrice = $elementPrice->add(
                $priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($prices)->multiply($multi));
        }

        // add preferred price and apply multiplier
        return $elementPrice;

    }

    public function getAdditionalInformation(
        PriceCalculator $priceCalculator,
        AptoUuid $sectionId,
        AptoUuid $elementId,
        ElementDefinition $elementDefinition,
        Money $elementPrice,
        Money $basePrice
    ): array {
        $items = $this->selectBoxItemFinder->findByElementId($elementId->getId());
        $customerGroupId = $priceCalculator->getCustomerGroup()['id'];
        $fallbackCustomerGroup = $priceCalculator->getFallbackCustomerGroupOrNull();
        $fallbackCustomerGroupId = null !== $fallbackCustomerGroup ? $fallbackCustomerGroup['id'] : null;

        $itemPrices = [];
        $formatPricePaths = [];

        foreach ($items['data'] as $item) {
            // get price data
            $prices = $this->selectBoxItemFinder->findPrice(
                $item['id'],
                $customerGroupId,
                $fallbackCustomerGroupId,
                $elementPrice->getCurrency()->getCode(),
                $priceCalculator->getFallbackCurrency()->getCode()
            );

            $itemPrices[$item['id']] = $priceCalculator->getTaxCalculator()->getDisplayPrice(
                $elementPrice->add(
                    $priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($prices)
                )
            );

            $formatPricePaths[$item['id']] = [
                'apto-plugin-live-price',
                'price',
                $item['id']
            ];
        }

        return [
            'formatPricePaths' => $formatPricePaths,
            'apto-plugin-live-price' => [
                'price' => $itemPrices
            ]
        ];
    }
}