<?php

namespace Apto\Plugins\PartsList\Domain\Core\Service;

use Money\Currency;
use Money\Money;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\Formula\FormulaParser;
use Apto\Catalog\Application\Core\Service\Formula\NoProductIdGivenException;
use Apto\Catalog\Application\Core\Service\Formula\NoStateGivenException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Service\Formula\Exception\FormulaParserException;
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

class ConfigurationPartsList
{
    /**
     * @var PartRepository
     */
    private PartRepository $partRepository;

    /**
     * @var FormulaParser
     */
    private FormulaParser $formulaParser;

    /**
     * @param PartRepository $partRepository
     * @param FormulaParser $formulaParser
     */
    public function __construct(PartRepository $partRepository, FormulaParser $formulaParser)
    {
        $this->partRepository = $partRepository;
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
     * @throws CircularReferenceException
     * @throws FormulaParserException
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
     * @throws CircularReferenceException
     * @throws FormulaParserException
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
     * @param $computedValues
     * @param string|null $categoryId
     * @return array
     * @throws CircularReferenceException
     * @throws FormulaParserException
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    public function getBasicList(AptoUuid $productId, State $state, Currency $currency, string $customerGroupId, $fallbackCustomerGroupId, AptoLocale $locale, $computedValues = [], ?string $categoryId = null)
    {
        $this->formulaParser->setState($state);
        $this->formulaParser->setProductId($productId);
        $usages = $this->getUsages($productId, $state, $computedValues);
        $list = [];
        /** @var Usage $usage */
        foreach ($usages as $usage) {
            if ($categoryId && $categoryId !== $usage->getPart()->getCategory()->getId()->getId()) {
                continue;
            }

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
     * @throws CircularReferenceException
     * @throws FormulaParserException
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
            'id' => $part->getId(),
            'partNumber' => $part->getPartNumber(),
            'description' => $part->getDescription()->getTranslation($locale)->getValue(),
            'partName' => $part->getName()->getTranslation($locale)->getValue(),
            'quantity' => str_replace('.', ',', (string) $this->getQuantity($usage, 2)),
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
     * @throws CircularReferenceException
     * @throws FormulaParserException
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
    private function getUsages(AptoUuid $productId, State $state, array $computedValues = []): array
    {
        $sections = $state->getSectionIds();
        $elements = $state->getElementList();
        $elementIds = $state->getElementIds();

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
                if (in_array($elementUsage->getUsageForUuid()->getId(), $elementIds)) {
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
    private function isRuleActive(RuleUsage $ruleUsage, AptoUuid $productId, State $state, array $computedValues = []): bool
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
}
