<?php

namespace Apto\Catalog\Application\Core\Service\ComputedProductValue;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\Alias;
use Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue\ComputedProductValue;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

class ComputedProductValueCalculator
{
    /**
     * @var ProductRepository
     * @deprecated remove repository after implementing ComputedProductValue value-objects
     */
    private $productRepository;

    /**
     * @var MediaFileSystemConnector
     */
    private $mediaFileSystem;

    /**
     * @param ProductRepository $productRepository
     * @param MediaFileSystemConnector $mediaFileSystem
     */
    public function __construct(
        ProductRepository $productRepository,
        MediaFileSystemConnector $mediaFileSystem
    ) {
        $this->productRepository = $productRepository;
        $this->mediaFileSystem = $mediaFileSystem;
    }

    /**
     * Checks in backend 'Berechnete Werte'
     *
     * @param Product $product
     * @param State $state
     * @param bool $indexedById
     * @return array
     * @throws CircularReferenceException
     * @throws InvalidUuidException
     * @deprecated use calculateComputedValues
     */
    public function calculateComputedValuesByProduct(Product $product, State $state, bool $indexedById = false): array
    {
        $values = [];
        $valuesById = [];

        /* @var ComputedProductValue $value */
        $orderedValues = $this->orderCalculatedValues($product->getComputedProductValues()->getValues());

        foreach ($orderedValues as $value) {
            $calculated = $value->getValue($state, $values, $this->mediaFileSystem);
            if ($indexedById) {
                $valuesById[$value->getId()->getId()] = $calculated;
            }
            $values[$value->getName()] = $calculated;
        }

        // calculate repetition values
        $repetitionValues = [];
        $repetitionValuesById = [];
        foreach ($orderedValues as $value) {
            for ($repetition = 1; $repetition < $this->getRepetitions($product, $value, $values); $repetition++) {
                $repetitionSuffix = '[' . $repetition . ']';
                $calculated = $value->getValue($state, $values, $this->mediaFileSystem, $repetition);
                if ($indexedById) {
                    $repetitionValuesById[$value->getId()->getId() . $repetitionSuffix] = $calculated;
                }
                $repetitionValues[$value->getName() . $repetitionSuffix] = $calculated;
            }
        }

        // merge repetition values with default values
        $values = array_merge($values, $repetitionValues);
        $valuesById = array_merge($valuesById, $repetitionValuesById);

        return $indexedById ? $valuesById : $values;
    }

    /**
     * @param string $productId
     * @param State $state
     * @param bool $indexedById
     * @return array
     * @throws CircularReferenceException
     * @throws InvalidUuidException
     * @todo refactor method
     */
    public function calculateComputedValues(string $productId, State $state, bool $indexedById = false): array
    {
        $product = $this->productRepository->findById($productId);
        if ($product === null) {
            return [];
        }

        return $this->calculateComputedValuesByProduct($product, $state, $indexedById);
    }

    /**
     * @param ComputedProductValue[] $computedProductValues
     * @throws CircularReferenceException
     */
    public function checkForCircularReference(array $computedProductValues) {
        $this->orderCalculatedValues($computedProductValues);
    }

    /**
     * @param array $values
     * @return array
     * @throws CircularReferenceException
     */
    private function orderCalculatedValues(array $values)
    {
        // condition to move to calculated value
        // formula has not computedValuePlaceholder || all computedValuePlaceholder already in $calculated
        // break condition is a static counter 10000
        $notCalculated = [
            't1' => '1',
            't2' => 't1',
            't3' => 't2',
            't4' => 't3',
            't5' => 't3',
        ];

        $calculated = [];

        $orderedValues = [];
        $dependentValues = [];
        foreach ($values as $value) {
            $variables = $this->getNestedVariables($value);
            if (sizeof($variables[0]) > 0) {
                $dependentValues[] = $value;
            }
            else {
                $orderedValues[] = $value;
            }
        }
        return $this->orderDependentValues($dependentValues, $orderedValues);
    }

    /**
     * @param $dependentValues
     * @param $orderedValues
     * @param int $counter
     * @return array
     * @throws CircularReferenceException
     */
    private function orderDependentValues($dependentValues, $orderedValues, int $counter = 0): array
    {
        // TODO: Currently fires on 100 Loops, there must be a better way to detect self/circular references
        if ($counter === 100) {
            throw new CircularReferenceException('Too many Loops, possible self/circular reference');
        }
        foreach ($dependentValues as $key => $value) {
            $variables = $this->getNestedVariables($value);
            $calculatedCount = 0;
            foreach ($variables[0] as $variable) {
                foreach ($orderedValues as $orderedValue) {
                    $variableName = str_replace('{', '', str_replace('}', '', $variable));
                    if ($variableName === $orderedValue->getName()) {
                        $calculatedCount++;
                    }
                }
            }
            if ($calculatedCount === sizeof($variables[0])) {
                $orderedValues[] = $value;
                unset($dependentValues[$key]);
                $dependentValues = array_values($dependentValues);
            }
        }
        if (sizeof($dependentValues) === 0) {
            return $orderedValues;
        }
        else {
            $counter++;
            return $this->orderDependentValues($dependentValues, $orderedValues, $counter);
        }
    }

    /**
     * @param ComputedProductValue $value
     * @return array
     */
    private function getNestedVariables(ComputedProductValue $value)
    {
        $pattern = '/{[^_]*?}/'; // search for {name}, where name does not contain any underscore (_)
        $variables = [];
        preg_match_all($pattern, $value->getFormula(), $variables);
        return $variables;
    }

    /**
     * @param Product $product
     * @param ComputedProductValue $value
     * @param array $values
     * @return int
     * @throws InvalidUuidException
     */
    private function getRepetitions(Product $product, ComputedProductValue $value, array $values): int
    {
        $repeatableSections = [];
        /** @var Alias $alias */
        foreach ($value->getAliases() as $alias) {
            $sectionUuid = new AptoUuid($alias->getSectionId());
            $repeatable = $product->getSectionRepeatable($sectionUuid);

            if (null === $repeatable) {
                continue;
            }

            if ($repeatable->isRepeatable() && array_key_exists($repeatable->getCalculatedValueName(), $values)) {
                $repeatableSections[$alias->getSectionId()] = $values[$repeatable->getCalculatedValueName()];
            }
        }

        $repeatableSections = array_values($repeatableSections);

        // We expect to have one, and ONLY one, section in $repeatableSections
        if (count($repeatableSections) === 1) {
            return $repeatableSections[0];
        }

        return 0;
    }
}
