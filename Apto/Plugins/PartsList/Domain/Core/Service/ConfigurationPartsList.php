<?php

namespace Apto\Plugins\PartsList\Domain\Core\Service;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\Formula\FormulaParser;
use Apto\Catalog\Application\Core\Service\Formula\NoProductIdGivenException;
use Apto\Catalog\Application\Core\Service\Formula\NoStateGivenException;
use Apto\Catalog\Application\Core\Service\StatePrice\StatePriceService;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\PartRepository;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\ElementUsage;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\ExplicitUsage;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\ProductUsage;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\Quantity;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\RuleCondition;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\RuleUsage;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\SectionUsage;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage\Usage;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

class ConfigurationPartsList
{
    /**
     * @var PartRepository
     */
    private $partRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var StatePriceService
     */
    private $statePriceService;

    /**
     * @var FormulaParser
     */
    private $formulaParser;

    /**
     * ConfigurationPartsList constructor.
     * @param PartRepository $partRepository
     * @param ProductRepository $productRepository
     * @param StatePriceService $statePriceService
     * @param FormulaParser $formulaParser
     */
    public function __construct(PartRepository $partRepository, ProductRepository $productRepository, StatePriceService $statePriceService, FormulaParser $formulaParser)
    {
        $this->partRepository = $partRepository;
        $this->productRepository = $productRepository;
        $this->statePriceService = $statePriceService;
        $this->formulaParser = $formulaParser;
    }

    /**
     * @param AptoUuid $productId
     * @param State $state
     * @param Currency $currency
     * @param string $customerGroupId
     * @param $fallbackCustomerGroupId
     * @param array $computedValues
     * @return Money
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    public function getTotalPrice(AptoUuid $productId, State $state, Currency $currency, string $customerGroupId, $fallbackCustomerGroupId, array $computedValues = [])
    {
        $sum = new Money('0', $currency);
        $usages = $this->getUsages($productId, $state, $computedValues);
        $this->formulaParser->setState($state);
        $this->formulaParser->setProductId($productId);
        /** @var Usage $usage */
        foreach ($usages as $usage) {
            $sum = $sum->add($this->getPartPrice($usage->getPart(), $usage, $currency, $customerGroupId, $fallbackCustomerGroupId));
        }

        return $sum;
    }

    /**
     * @param Part $part
     * @param Usage $usage
     * @param Currency $currency
     * @param string $customerGroupId
     * @param $fallbackCustomerGroupId
     * @param bool $multiplied
     * @return Money
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    private function getPartPrice(Part $part, Usage $usage, Currency $currency, string $customerGroupId, $fallbackCustomerGroupId, bool $multiplied = true )
    {
        $basePrice = $part->getAptoPrice($currency, new AptoUuid($customerGroupId));

        if (null === $basePrice && null !== $fallbackCustomerGroupId)
        {
            $basePrice = $part->getAptoPrice($currency, new AptoUuid($fallbackCustomerGroupId));
        }
        if (null !== $basePrice) {
            $quantity = $this->getQuantity($usage, 2);

            if ($multiplied) {
                $baseQuantity = $part->getBaseQuantity();

                return $basePrice->multiply($quantity / floatval($baseQuantity));
            }
            return $basePrice;

        }
        return new Money('0', $currency);
    }

    /**
     * @param AptoUuid $productId
     * @param State $state
     * @param Currency $currency
     * @param string $customerGroupId
     * @param $fallbackCustomerGroupId
     * @param AptoLocale $locale
     * @param array $computedValues
     * @return array
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    public function getBasicList(AptoUuid $productId, State $state, Currency $currency, string $customerGroupId, $fallbackCustomerGroupId, AptoLocale $locale, $computedValues = [])
    {
        $this->formulaParser->setState($state);
        $this->formulaParser->setProductId($productId);
        $usages = $this->getUsages($productId, $state, $computedValues);

        $list = [];
        /** @var Usage $usage */
        foreach ($usages as $usage) {
            $list[] = $this->makeListItem($usage->getPart(), $usage, $currency, $customerGroupId, $fallbackCustomerGroupId, $locale);
        }
        return $list;
    }

    /**
     * @param Part $part
     * @param Usage $usage
     * @param Currency $currency
     * @param string $customerGroupId
     * @param $fallbackCustomerGroupId
     * @param AptoLocale $locale
     * @return array
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    private function makeListItem(Part $part, Usage $usage, Currency $currency, string $customerGroupId, $fallbackCustomerGroupId, AptoLocale $locale)
    {
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        $formattedBasePartPrice = $moneyFormatter->format($this->getPartPrice($part, $usage, $currency, $customerGroupId, $fallbackCustomerGroupId, false));
        $formattedPartPrice = $moneyFormatter->format($this->getPartPrice($part, $usage, $currency, $customerGroupId, $fallbackCustomerGroupId));
        return [
            'partNumber' => $part->getPartNumber(),
            'partName' => $part->getName()->getTranslation($locale)->getValue(),
            'quantity' => str_replace('.', ',', $this->getQuantity($usage, 2)),
            'unit' => $part->getUnit() ? $part->getUnit()->getUnit() : '',
            'baseQuantity' => $part->getBaseQuantity(),
            'itemPrice' => str_replace('.', ',', $formattedBasePartPrice),
            'itemPriceTotal' => str_replace('.', ',', $formattedPartPrice)
        ];
    }

    /**
     * @param Usage $usage
     * @param int $precision
     * @return float
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    private function getQuantity(Usage $usage, int $precision) :float
    {
        return $this->formulaParser->calculateFormula($usage->getQuantity()->getQuantity(), true, $precision);
    }

    /**
     * @param AptoUuid $productId
     * @param State $state
     * @param array $computedValues
     * @return array
     * @throws InvalidUuidException
     */
    private function getUsages(AptoUuid $productId, State $state, array $computedValues = [])
    {
        $sections = $state->getSectionList();
        $elements = $state->getElementList();
        $elementIds = $state->getElementIds();

        $elementSections = $this->getElementSections($state, $elements);

        $parts = $this->partRepository->findByUsages(
            [$productId->getId()],
            array_keys($sections),
            $elementIds
        );

        $additionalParts = [];
        foreach ($elements as $element) {
            if (!empty($element['values']) && key_exists('partsList', $element['values'])) {
                foreach ($element['values']['partsList'] as $partNumber => $quantity) {
                    if ($quantity)  {
                        $additionalParts[$partNumber] = $quantity;
                    }
                }
            }
        }
        $usages = [];
        /** @var Part $part */
        foreach ($parts as $i => $part) {
            /** @var ProductUsage $productUsage */
            foreach ($part->getProductUsages() as $productUsage) {
                if ($productUsage->getUsageForUuid()->getId() === $productId->getId()) {
                    $usages[] = $productUsage;
                }
            }
            /** @var SectionUsage $sectionUsage */
            foreach ($part->getSectionUsages() as $sectionUsage) {
                $sectionId = $sectionUsage->getUsageForUuid();
                if (array_key_exists($sectionId->getId(), $sections)) {
                    $usages[] = $sectionUsage;
                }
            }
            /** @var ElementUsage $elementUsage */
            foreach ($part->getElementUsages() as $elementUsage) {
                if (array_key_exists($elementUsage->getUsageForUuid()->getId(), $elementSections)) {
                    $usages[] = $elementUsage;
                }
            }
            /** @var RuleUsage $ruleUsage */
            foreach ($part->getRuleUsages() as $ruleUsage) {
                if ($this->isRuleActive($ruleUsage, $productId, $state, $computedValues)) {
                    $usages[] = $ruleUsage;
                }
            }
        }
        foreach ($additionalParts as $partNumber => $quantity) {
            $part = $this->partRepository->findByPartNumber($partNumber);
            if (null === $part) {
                continue;
            }
            $newExplicitUsage = new ExplicitUsage(
                $part,
                new AptoUuid(),
                new Quantity($quantity),
                $productId
            );
            $usages[] = $newExplicitUsage;
        }
        return $usages;
    }

    /**
     * @param RuleUsage $ruleUsage
     * @param AptoUuid $productId
     * @param State $state
     * @param array $computedValues
     * @return bool
     * @throws InvalidUuidException
     */
    private function isRuleActive(RuleUsage $ruleUsage, AptoUuid $productId, State $state, array $computedValues = [])
    {
        $operator = $ruleUsage->getConditionsOperator();
        $fulfilled = false;
        if (!$ruleUsage->isActive()) {
            return false;
        }
        /** @var RuleCondition $condition */
        foreach ($ruleUsage->getConditions() as $condition) {
            if (!$condition->isFulfilledBy($productId, $state, $computedValues)){
                if ($operator === 0) {
                    return false;
                }
            }
            else {
                $fulfilled = true;
            }
        }
        return $fulfilled;
    }

    /**
     * Return array in form:
     * array [
     *  elementId => sectionId
     * ]
     *
     * @param State $state
     * @param array $elements
     *
     * @return array
     */
    private function getElementSections(State $state, array $elements): array
    {
        $elementSections = [];
        foreach ($elements as $element) {
            foreach ($state->getStateWithoutParameters() as $section) {
                // @todo handle the case when we have multiple elements with the same elementId
                if (array_key_exists($element['elementId'], $section)) {
                    $elementSections[$element['elementId']] = $section['sectionId'];
                }
            }
        }

        return $elementSections;
    }

    /**
     * @param ElementDefinition $elementDefinition
     * @param State $state
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @return array
     */
    private function getSelectedValues(ElementDefinition $elementDefinition, State $state, AptoUuid $sectionId, AptoUuid $elementId): array
    {
        $selectedValues = [];
        foreach (array_keys($elementDefinition->getSelectableValues()) as $selectableValue) {
            $selectedValues[$selectableValue] = $state->getValue($sectionId, $elementId, $selectableValue);
        }
        return $selectedValues;
    }

    /**
     * @param $value
     * @return mixed
     */
    private function removeTagsAndLineBreaks($value)
    {
        return str_replace(['\r\n', '\r', '\n'], ' ', strip_tags($value));
    }
}
