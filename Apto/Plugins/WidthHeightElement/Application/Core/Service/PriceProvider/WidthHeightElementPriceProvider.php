<?php
namespace Apto\Plugins\WidthHeightElement\Application\Core\Service\PriceProvider;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Core\Query\PriceMatrix\PriceMatrixFinder;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\BasePriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\AdditionalPriceInformationProvider;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\WidthHeightElement\Domain\Core\Model\Product\Element\WidthHeightElementDefinition;
use Money\Money;

class WidthHeightElementPriceProvider implements BasePriceProvider, AdditionalPriceInformationProvider
{
    /**
     * @var PriceMatrixFinder
     */
    private $priceMatrixFinder;

    /**
     * WidthHeightElementPriceProvider constructor.
     * @param PriceMatrixFinder $priceMatrixFinder
     */
    public function __construct(PriceMatrixFinder $priceMatrixFinder)
    {
        $this->priceMatrixFinder = $priceMatrixFinder;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClass(): string
    {
        return WidthHeightElementDefinition::class;
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @param Money $elementPrice
     * @return Money
     */
    public function getPrice(
        PriceCalculator $priceCalculator,
        AptoUuid $sectionId,
        AptoUuid $elementId,
        ElementDefinition $elementDefinition,
        Money $elementPrice
    ): Money {
        // set price calculator values
        $state = $priceCalculator->getState();
        $customerGroupId = $priceCalculator->getCustomerGroup()['id'];
        $fallbackCustomerGroup = $priceCalculator->getFallbackCustomerGroupOrNull();
        $fallbackCustomerGroupId = null !== $fallbackCustomerGroup ? $fallbackCustomerGroup['id'] : null;

        // get static values
        $staticValues = $elementDefinition->getStaticValues();
        $priceMatrixId = $staticValues['priceMatrix']['id'];

        // skip, if price matrix is not set
        if (!$priceMatrixId) {
            return $elementPrice;
        }

        // get value from state
        $selectedWidth = $state->getValue($sectionId, $elementId, 'width');
        $selectedHeight = $state->getValue($sectionId, $elementId, 'height');

        // skip, if value is not set
        if (
            (!$selectedWidth && $staticValues['renderingWidth'] !== 'none') ||
            (!$selectedHeight && $staticValues['renderingHeight'] !== 'none')
        ) {
            return $elementPrice;
        }

        // get price data
        $priceMatrixElementPrices = $this->priceMatrixFinder->findNextHigherPriceByColumnRowValue(
            $priceMatrixId,
            floatval($selectedWidth),
            floatval($selectedHeight),
            $customerGroupId,
            $fallbackCustomerGroupId,
            $elementPrice->getCurrency()->getCode(),
            $priceCalculator->getFallbackCurrency()->getCode()
        );

        // add preferred price
        return $elementPrice->add(
            $priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($priceMatrixElementPrices)
        );
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
        // set price calculator values
        $state = $priceCalculator->getState();
        $customerGroupId = $priceCalculator->getCustomerGroup()['id'];
        $fallbackCustomerGroup = $priceCalculator->getFallbackCustomerGroupOrNull();
        $fallbackCustomerGroupId = null !== $fallbackCustomerGroup ? $fallbackCustomerGroup['id'] : null;
        $additionalInformationResult = [];

        // get static values
        $staticValues = $elementDefinition->getStaticValues();
        $priceMatrixId = $staticValues['priceMatrix']['id'];

        // skip, if price matrix is not set
        if (!$priceMatrixId) {
            return [];
        }

        // calculate minimum price
        $minimumPrice = $this->getMinimumPrice($priceMatrixId, $priceCalculator);

        // get value from state
        $selectedWidth = $state->getValue($sectionId, $elementId, 'width');
        $selectedHeight = $state->getValue($sectionId, $elementId, 'height');

        // skip, if value is not set
        if (
            (!$selectedWidth && $staticValues['renderingWidth'] !== 'none') ||
            (!$selectedHeight && $staticValues['renderingHeight'] !== 'none')
        ) {
            return $this->addLivePriceAdditionalInformation($additionalInformationResult, $minimumPrice);
        }

        // get additional information data
        $additionalInformation = $this->priceMatrixFinder->findAdditionalInformationByColumnRowValue(
            $priceMatrixId,
            floatval($selectedWidth),
            floatval($selectedHeight),
            $customerGroupId,
            $fallbackCustomerGroupId,
            $elementPrice->getCurrency()->getCode(),
            $priceCalculator->getFallbackCurrency()->getCode()
        );

        // prefer customer group before fallback customer group, default to an empty array
        if (array_key_exists($customerGroupId, $additionalInformation)) {
            $additionalInformationResult = $additionalInformation[$customerGroupId];
        } elseif (array_key_exists($fallbackCustomerGroupId, $additionalInformation)) {
            $additionalInformationResult = $additionalInformation[$fallbackCustomerGroupId];
        }

        return $this->addLivePriceAdditionalInformation($additionalInformationResult, $minimumPrice);
    }

    /**
     * @param array $additionalInformation
     * @param Money $minimumPrice
     * @return array
     */
    private function addLivePriceAdditionalInformation(array $additionalInformation, Money $minimumPrice): array
    {
        $additionalInformation['formatPricePaths'] = [
            [
                'apto-plugin-live-price',
                'price'
            ]
        ];

        $additionalInformation['apto-plugin-live-price']['price'] = $minimumPrice;

        return $additionalInformation;
    }

    /**
     * @param string $priceMatrixId
     * @param PriceCalculator $priceCalculator
     * @return Money
     */
    private function getMinimumPrice(string $priceMatrixId, PriceCalculator $priceCalculator): Money
    {
        $elements = $this->priceMatrixFinder->findElements($priceMatrixId);
        $minimumPrice = null;

        foreach ($elements['elements'] as $element) {
            if (!array_key_exists('aptoPrices', $element)) {
                continue;
            }

            $elementPrice = $priceCalculator->getTaxCalculator()->getDisplayPrice($priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($element['aptoPrices']));

            if ($minimumPrice === null) {
                $minimumPrice = $elementPrice;
                continue;
            }

            if ($elementPrice->getAmount() < $minimumPrice->getAmount() && $elementPrice->getAmount() > 0) {
                $minimumPrice = $elementPrice;
            }
        }

        return $minimumPrice ?? new Money(0, $priceCalculator->getCurrency());
    }
}