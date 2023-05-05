<?php
namespace Apto\Plugins\MaterialPickerElement\Application\Core\Service\PriceProvider;

use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Product\Element\MaterialPickerElementDefinition;
use Money\Money;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ElementPriceProvider;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool\PoolFinder;
use Apto\Plugins\MaterialPickerElement\Application\Core\Query\Material\MaterialFinder;
use Apto\Catalog\Application\Core\Query\PriceMatrix\PriceMatrixFinder;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;

class MaterialPickerElementPriceProvider implements ElementPriceProvider
{
    /**
     * @var PoolFinder
     */
    private $poolFinder;

    /**
     * @var MaterialFinder
     */
    private $materialFinder;

    /**
     * @var PriceMatrixFinder
     */
    private $priceMatrixFinder;

    /**
     * @var ComputedProductValueCalculator
     */
    protected $computedProductValueCalculator;

    /**
     * MaterialPickerElementPriceProvider constructor.
     * @param PoolFinder $poolFinder
     * @param MaterialFinder $materialFinder
     * @param PriceMatrixFinder $priceMatrixFinder
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     */
    public function __construct(
        PoolFinder $poolFinder,
        MaterialFinder $materialFinder,
        PriceMatrixFinder $priceMatrixFinder,
        ComputedProductValueCalculator $computedProductValueCalculator
    ) {
        $this->poolFinder = $poolFinder;
        $this->materialFinder = $materialFinder;
        $this->priceMatrixFinder = $priceMatrixFinder;
        $this->computedProductValueCalculator = $computedProductValueCalculator;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClass(): string
    {
        return MaterialPickerElementDefinition::class;
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param ElementDefinition $elementDefinition
     * @param Money $elementPrice
     * @param Money $basePrice
     * @return Money
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

        // get static values
        $staticValues = $elementDefinition->getStaticValues();
        $poolId = $staticValues['poolId'];
        $secondaryMaterialActive = $staticValues['secondaryMaterialActive'];

        $selectedMaterialIds = [];
        $selectedMaterialIdsSecondary = [];
        if ($staticValues['allowMultiple']) {
            foreach ($state->getValue($sectionId, $elementId, 'materials') as $material) {
                $selectedMaterialIds[] = $material['id'];
            }

            foreach ($state->getValue($sectionId, $elementId, 'materialsSecondary') as $materialSecondary) {
                $selectedMaterialIdsSecondary[] = $materialSecondary['id'];
            }
        } else {
            $materialId = $state->getValue($sectionId, $elementId, 'materialId');
            if ($materialId) {
                $selectedMaterialIds[] = $materialId;
            }

            $materialIdSecondary = $state->getValue($sectionId, $elementId, 'materialIdSecondary');
            if ($materialIdSecondary) {
                $selectedMaterialIdsSecondary[] = $materialIdSecondary;
            }
        }

        // add static material prices
        $elementPrice = $this->addStaticMaterialPrices(
            $priceCalculator,
            $elementPrice,
            $sectionId,
            $elementId,
            $state,
            $staticValues['allowMultiple']
        );

        // skip, if poolId is not set or primary material id is not set
        if (!$poolId || empty($selectedMaterialIds)) {
            return $elementPrice;
        }

        foreach ($selectedMaterialIds as $selectedMaterialId)
        {
            // get priceGroup for primary material
            $priceGroup = $this->poolFinder->findPriceGroup(
                $poolId,
                $selectedMaterialId
            );

            // skip, if price group not found
            if (null === $priceGroup) {
                continue;
            }

            // add price if it could be determined
            if (array_key_exists('additionalCharge', $priceGroup)) {
                $elementPrice = $elementPrice->add($basePrice->multiply($priceGroup['additionalCharge'] / 100));
            }

            // add price matrix price
            if (
                array_key_exists('priceMatrixId', $priceGroup) &&
                array_key_exists('priceMatrixRow', $priceGroup) &&
                array_key_exists('priceMatrixColumn', $priceGroup)
            ) {
                $elementPrice = $this->addPriceMatrixPrice(
                    $priceCalculator,
                    $elementPrice,
                    $priceGroup['priceMatrixId'],
                    $priceGroup['priceMatrixRow'],
                    $priceGroup['priceMatrixColumn'],
                    $priceGroup['priceMatrixPricePostProcess']
                );
            }
        }

        // skip, if secondary material is not active or not selected
        if (!$secondaryMaterialActive || empty($selectedMaterialIdsSecondary)) {
            return $elementPrice;
        }

        // add extra charge for secondary material selected
        $secondaryMaterialAdditionalCharge = 1500;
        if (array_key_exists('secondaryMaterialAdditionalCharge', $staticValues)) {
            $secondaryMaterialAdditionalCharge = $staticValues['secondaryMaterialAdditionalCharge'];
        }
        $elementPrice = $elementPrice->add(new Money($secondaryMaterialAdditionalCharge, $elementPrice->getCurrency()));

        foreach ($selectedMaterialIdsSecondary as $selectedMaterialIdSecondary)
        {
            // get priceGroup for secondary material
            $priceGroupSecondary = $this->poolFinder->findPriceGroup(
                $poolId,
                $selectedMaterialIdSecondary
            );

            // add price if it could be determined
            if (array_key_exists('additionalCharge', $priceGroupSecondary)) {
                $elementPrice = $elementPrice->add($basePrice->multiply($priceGroupSecondary['additionalCharge'] / 100));
            }

            // add price matrix price for secondary price group
            if (
                array_key_exists('priceMatrixId', $priceGroupSecondary) &&
                array_key_exists('priceMatrixRow', $priceGroupSecondary) &&
                array_key_exists('priceMatrixColumn', $priceGroupSecondary)
            ) {
                $elementPrice = $this->addPriceMatrixPrice(
                    $priceCalculator,
                    $elementPrice,
                    $priceGroupSecondary['priceMatrixId'],
                    $priceGroupSecondary['priceMatrixRow'],
                    $priceGroupSecondary['priceMatrixColumn'],
                    $priceGroupSecondary['priceMatrixPricePostProcess']
                );
            }
        }

        return $elementPrice;
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param Money $elementPrice
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param State $state
     * @param bool $allowMultiple
     * @return Money
     * @throws InvalidUuidException
     */
    private function addStaticMaterialPrices(
        PriceCalculator $priceCalculator,
        Money $elementPrice,
        AptoUuid $sectionId,
        AptoUuid $elementId,
        State $state,
        bool $allowMultiple
    ): Money {
        if (false === $allowMultiple) {
            // get value from state
            $selectedMaterialId = $state->getValue($sectionId, $elementId, 'materialId');
            $selectedMaterialIdSecondary = $state->getValue($sectionId, $elementId, 'materialIdSecondary');

            if ($selectedMaterialId) {
                $elementPrice = $this->addMaterialPrice($priceCalculator, $elementPrice, new AptoUuid($selectedMaterialId));
            }

            if ($selectedMaterialIdSecondary) {
                $elementPrice = $this->addMaterialPrice($priceCalculator, $elementPrice, new AptoUuid($selectedMaterialIdSecondary));
            }
        } else {
            foreach ($state->getValue($sectionId, $elementId, 'materials') as $material) {
                $elementPrice = $this->addMaterialPrice($priceCalculator, $elementPrice, new AptoUuid($material['id']));
            }

            foreach ($state->getValue($sectionId, $elementId, 'materialsSecondary') as $materialSecondary) {
                $elementPrice = $this->addMaterialPrice($priceCalculator, $elementPrice, new AptoUuid($materialSecondary['id']));
            }
        }

        return $elementPrice;
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param Money $elementPrice
     * @param AptoUuid $materialId
     * @return Money
     */
    private function addMaterialPrice(PriceCalculator $priceCalculator, Money $elementPrice, AptoUuid $materialId): Money
    {
        $customerGroupId = $priceCalculator->getCustomerGroup()['id'];
        $fallbackCustomerGroup = $priceCalculator->getFallbackCustomerGroupOrNull();
        $fallbackCustomerGroupId = null !== $fallbackCustomerGroup ? $fallbackCustomerGroup['id'] : null;

        // get price data
        $materialPrices = $this->materialFinder->findPrice(
            $materialId->getId(),
            $customerGroupId,
            $fallbackCustomerGroupId,
            $elementPrice->getCurrency()->getCode(),
            $priceCalculator->getFallbackCurrency()->getCode()
        );
        $materialPrice = $priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($materialPrices);

        return $elementPrice->add($materialPrice);
    }

    /**
     * @param PriceCalculator $priceCalculator
     * @param Money $elementPrice
     * @param string|null $priceMatrixId
     * @param string|null $rowFormula
     * @param string|null $columnFormula
     * @return Money
     */
    private function addPriceMatrixPrice(
        PriceCalculator $priceCalculator,
        Money $elementPrice,
        ?string $priceMatrixId,
        ?string $rowFormula,
        ?string $columnFormula,
        ?string $pricePostProcessFormula
    ): Money {
        if (null === $priceMatrixId || null === $rowFormula || null === $columnFormula) {
            return $elementPrice;
        }

        // get customer group
        $customerGroupId = $priceCalculator->getCustomerGroup()['id'];
        $fallbackCustomerGroup = $priceCalculator->getFallbackCustomerGroupOrNull();
        $fallbackCustomerGroupId = null !== $fallbackCustomerGroup ? $fallbackCustomerGroup['id'] : null;

        // get computed product values
        $computedValues = $this->computedProductValueCalculator->calculateComputedValues(
            $priceCalculator->getProductId(),
            $priceCalculator->getState()
        );

        // cannot calculate value if a computed product value was not found
        $rowValue = $this->calculateFormula($rowFormula, $computedValues);
        $columnValue = $this->calculateFormula($columnFormula, $computedValues);
        if (null === $rowValue || null === $columnValue) {
            return $elementPrice;
        }

        // get price data
        $priceMatrixElementPrices = $this->priceMatrixFinder->findNextHigherPriceByColumnRowValue(
            $priceMatrixId,
            $columnValue,
            $rowValue,
            $customerGroupId,
            $fallbackCustomerGroupId,
            $elementPrice->getCurrency()->getCode(),
            $priceCalculator->getFallbackCurrency()->getCode()
        );

        $priceFromPriceMatrix = $priceCalculator->getTaxAdaptedPriceByPreferredCustomerGroup($priceMatrixElementPrices);

        // if {_preis_} is provided, corresponding formula should be considered as well when calculating the price
        if ($pricePostProcessFormula) {
            $computedValues['_preis_'] = $priceFromPriceMatrix->getAmount();
            $pricePostProcessValue = round($this->calculateFormula($pricePostProcessFormula, $computedValues));
            $priceFromPriceMatrix = new Money($pricePostProcessValue, $priceFromPriceMatrix->getCurrency());
        }

        // add preferred price
        return $elementPrice->add(
            $priceFromPriceMatrix
        );
    }

    /**
     * @param string $formula
     * @param array $computedValues
     * @return float|null
     */
    private function calculateFormula(string $formula, array $computedValues): ?float
    {
        // get required computed product values from formula
        $pattern = '/\\{.*?\\}/';
        $variables = [];
        preg_match_all($pattern, $formula, $variables);

        foreach ($variables[0] as $variable) {
            $variableName = str_replace('{', '', str_replace('}', '', $variable));

            // cannot calculate formula if a computed product value was not found
            if (!array_key_exists($variableName, $computedValues)) {
                return null;
            }

            $formula = str_replace($variable, $computedValues[$variableName], $formula);
        }

        return floatval(math_eval($formula));
    }
}
