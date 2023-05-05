<?php

namespace Apto\Catalog\Application\Core\Service\ComputedProductValue;

use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
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
}
