<?php
namespace Apto\Plugins\AreaElement\Application\Core\Service\PriceProvider;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\Math\Calculator;
use Apto\Catalog\Application\Core\Query\PriceMatrix\PriceMatrixFinder;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\BasePriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\AdditionalPriceInformationProvider;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\AreaElement\Domain\Core\Model\Product\Element\AreaElementDefinition;
use Money\Money;

class AreaElementPriceProvider implements BasePriceProvider, AdditionalPriceInformationProvider
{
    const formulaChars = 'abcdfghijklmnopqrstuvwxyz';

    /**
     * @var PriceMatrixFinder
     */
    private $priceMatrixFinder;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * AreaElementPriceProvider constructor.
     * @param PriceMatrixFinder $priceMatrixFinder
     */
    public function __construct(PriceMatrixFinder $priceMatrixFinder)
    {
        $this->priceMatrixFinder = $priceMatrixFinder;
        $this->calculator = new Calculator();
    }

    /**
     * @return string
     */
    public function getElementDefinitionClass(): string
    {
        return AreaElementDefinition::class;
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @param Money $elementPrice
     * @return Money
     * @throws InvalidUuidException
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
        $priceMatrix = $staticValues['priceMatrix'];

        // calculate price multiplication
        $priceMultiplication = '1';
        if (isset($staticValues['priceMultiplication'])) {
            $priceMultiplication = $this->getPriceMultiplication(
                $priceCalculator,
                $staticValues['priceMultiplication'],
                $elementDefinition,
                $sectionId,
                $elementId
            );
        }

        // skip, if price matrix is not set
        if (!$priceMatrix['id'] || !$priceMatrix['row'] || !$priceMatrix['column'] || !$this->hasStateRequiredValues($state, $elementDefinition, $sectionId, $elementId)) {
            return $elementPrice->multiply($priceMultiplication);
        }

        // get formula objects
        $rowValue = $this->calculateMatrixFormula($priceMatrix['row'], $state, $elementDefinition, $sectionId, $elementId);
        $columnValue = $this->calculateMatrixFormula($priceMatrix['column'], $state, $elementDefinition, $sectionId, $elementId);

        // get price data
        $priceMatrixElementPrices = $this->priceMatrixFinder->findNextHigherPriceByColumnRowValue(
            $priceMatrix['id'],
            $columnValue,
            $rowValue,
            $customerGroupId,
            $fallbackCustomerGroupId,
            $elementPrice->getCurrency()->getCode(),
            $priceCalculator->getFallbackCurrency()->getCode()
        );

        // get tax adapted price
        $taxAdaptedPrice = $priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($priceMatrixElementPrices);

        // add preferred price
        return $elementPrice->add(
            $taxAdaptedPrice
        )->multiply($priceMultiplication);
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @param Money $elementPrice
     * @param Money $basePrice
     * @return array
     * @throws InvalidUuidException
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
        $priceMatrix = $staticValues['priceMatrix'];

        // skip, if price matrix is not set
        if (!$priceMatrix['id'] || !$priceMatrix['row'] || !$priceMatrix['column']) {
            return [];
        }

        // calculate price multiplication
        $priceMultiplication = '1';
        if (isset($staticValues['priceMultiplication'])) {
            $priceMultiplication = $this->getPriceMultiplication(
                $priceCalculator,
                $staticValues['priceMultiplication'],
                $elementDefinition,
                $sectionId,
                $elementId
            );
        }

        // calculate minimum price
        $minimumPrice = $this->getMinimumPrice($priceMatrix['id'], $priceCalculator)->multiply($priceMultiplication);

        // skip, if state has not all required values
        if (!$this->hasStateRequiredValues($state, $elementDefinition, $sectionId, $elementId)) {
            return $this->addLivePriceAdditionalInformation($additionalInformationResult, $minimumPrice);;
        }

        // get formula objects
        $rowValue = $this->calculateMatrixFormula($priceMatrix['row'], $state, $elementDefinition, $sectionId, $elementId);
        $columnValue = $this->calculateMatrixFormula($priceMatrix['column'], $state, $elementDefinition, $sectionId, $elementId);

        // get additional information data
        $additionalInformation = $this->priceMatrixFinder->findAdditionalInformationByColumnRowValue(
            $priceMatrix['id'],
            $columnValue,
            $rowValue,
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
     * @param PriceCalculator $priceCalculator
     * @param array $priceMultiplication
     * @param ElementDefinition $elementDefinition
     * @param AptoUuid $ownSectionId
     * @param AptoUuid $ownElementId
     * @return string
     * @throws InvalidUuidException
     */
    private function getPriceMultiplication(
        PriceCalculator $priceCalculator,
        array $priceMultiplication,
        ElementDefinition $elementDefinition,
        AptoUuid $ownSectionId,
        AptoUuid $ownElementId
    ): string {
        if (
            !array_key_exists('active', $priceMultiplication) ||
            !array_key_exists('baseValueFormula', $priceMultiplication) ||
            !array_key_exists('factor', $priceMultiplication) ||
            !$priceMultiplication['active'] ||
            !$priceMultiplication['baseValueFormula'] ||
            !$priceMultiplication['factor']
        ) {
            return '1';
        }

        $state = $priceCalculator->getState();
        $pattern = '/\\{.*?\\}/';
        $formula = $priceMultiplication['baseValueFormula'];
        $references = [];

        preg_match_all($pattern, $formula, $references);

        foreach ($references[0] as $reference) {
            $propertyPath = explode('|', str_replace('{', '', str_replace('}', '', $reference)));
            $sectionId = null;
            $elementId = null;
            $property = null;

            // if we cant evaluate one match the whole formula is invalid an we return the default value 1
            if (count($propertyPath) !== 3) {
                return '1';
            }

            foreach ($priceCalculator->getElementIdIdentifierMapping() as $mapping) {
                if ($mapping['sectionIdentifier'] === $propertyPath[0] && $mapping['elementIdentifier'] === $propertyPath[1]) {
                    $sectionId = new AptoUuid($mapping['sectionId']);
                    $elementId = new AptoUuid($mapping['elementId']);
                    $property = $propertyPath[2];
                    break;
                }
            }

            // return default value if no element was found
            if ($sectionId === null || $elementId === null || $property === null) {
                return '1';
            }

            $replaceValue = $state->getValue($sectionId, $elementId, $property);
            if ($replaceValue === null) {
                return '1';
            }

            $formula = str_replace($reference, $state->getValue($sectionId, $elementId, $property), $formula);
        }

        $formulaFieldValues = $this->getFormulaFieldValues($state, $elementDefinition, $ownSectionId, $ownElementId);
        $formula = $this->convertFormula($elementDefinition, $formula, $formulaFieldValues);

        return $this->calculator->mul(
            $priceMultiplication['factor'],
            (string) math_eval($formula, $formulaFieldValues)
        );
    }

    /**
     * @param State $state
     * @param ElementDefinition $elementDefinition
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return bool
     */
    private function hasStateRequiredValues(State $state, ElementDefinition $elementDefinition, AptoUuid $sectionId, AptoUuid $elementId): bool
    {
        foreach ($elementDefinition->getStaticValues()['fields'] as $index => $field) {
            if (null === $state->getValue($sectionId, $elementId, 'field_' . $index)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $formula
     * @param State $state
     * @param ElementDefinition $elementDefinition
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return mixed
     */
    private function calculateMatrixFormula(string $formula, State $state, ElementDefinition $elementDefinition, AptoUuid $sectionId, AptoUuid $elementId)
    {
        // add +0 to formula string because Formula cant handle variable only strings
        $formulaFieldValues = $this->getFormulaFieldValues($state, $elementDefinition, $sectionId, $elementId);
        $formula = $this->convertFormula($elementDefinition, $formula, $formulaFieldValues);

        return math_eval($formula, $formulaFieldValues);
    }

    /**
     * @param ElementDefinition $elementDefinition
     * @param string $name
     * @param string $formula
     * @param array $formulaFieldValues
     * @return string
     */
    private function evaluateFormulaFunction(ElementDefinition $elementDefinition, string $name, string $formula, array $formulaFieldValues): string
    {
        $mapping = $this->getVariableMapping($elementDefinition);
        $pattern = '/' . $name . '\\(.*?\\)/';
        $references = [];

        preg_match_all($pattern, $formula, $references);

        foreach ($references[0] as $reference) {
            // remove function name and brackets
            $params = str_replace($name . '(', '', $reference);
            $params = str_replace(')', '', $params);

            // get params as array
            $params = explode(',', $params);

            // get parameter values
            $values = [];
            foreach ($params as $param) {
                $param = trim($param);

                if (!array_key_exists($param, $mapping)) {
                    continue;
                }
                $values[] = $formulaFieldValues[$mapping[$param]];
            }

            //print_r($values);
            // call methods and replace reference with calculated value
            switch ($name) {
                case 'min': {
                    $formula = str_replace($reference, min($values), $formula);
                    break;
                }
                case 'max': {
                    $formula = str_replace($reference, max($values), $formula);
                    break;
                }
            }
        }

        return $formula;
    }

    /**
     * @param State $state
     * @param ElementDefinition $elementDefinition
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return array
     */
    private function getFormulaFieldValues(State $state, ElementDefinition $elementDefinition, AptoUuid $sectionId, AptoUuid $elementId): array
    {
        $variableChars = str_split(self::formulaChars);
        $variables = [];
        foreach ($elementDefinition->getStaticValues()['fields'] as $index => $field) {
            $variableChar = $variableChars[$index];
            $variables[$variableChar] = $state->getValue($sectionId, $elementId, 'field_' . $index);
        }
        return $variables;
    }

    /**
     * @param ElementDefinition $elementDefinition
     * @param string $formula
     * @param array $formulaFieldValues
     * @return string
     */
    private function convertFormula(ElementDefinition $elementDefinition, string $formula, array $formulaFieldValues): string
    {
        $formula = $this->evaluateFormulaFunction($elementDefinition, 'min', $formula, $formulaFieldValues);
        $formula = $this->evaluateFormulaFunction($elementDefinition, 'max', $formula, $formulaFieldValues);
        $mapping = $this->getVariableMapping($elementDefinition);
        return str_replace(array_keys($mapping), array_values($mapping), $formula);
    }

    /**
     * @param ElementDefinition $elementDefinition
     * @return array
     */
    private function getVariableMapping(ElementDefinition $elementDefinition)
    {
        $variableChars = str_split(self::formulaChars);
        $fieldVariables = [];
        foreach ($elementDefinition->getStaticValues()['fields'] as $index => $field) {
            $fieldVariables['f' . ($index + 1)] = $variableChars[$index];
        }
        return $fieldVariables;
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
