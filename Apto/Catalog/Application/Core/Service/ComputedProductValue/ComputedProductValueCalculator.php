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
     *  Orders calculated values such that later we can put them one after another into the formula and calculate
     *  resulting values for each of them
     *
     *  first of course we put variables that have no reference to other variables, then variables that have reference
     *  to these "no reference" variables, then the others that have reference to these, and so on.
     *  We check also for circular references. We can have 3 types of circular references:
     *
     *  1. element references himself, element with the name t3 has value like: {t3} + 5
     *  2. element has reference to element which in turn has back reference on him:
     *       t3 element has value: {t5}
     *       t5 has value {t3}
     *  3. when element has reference on element that has reference to other element and that other
     *     element has reference back to current element:
     *       t3 element has value: {t6}
     *       t6 has value: {t5}
     *       t5 has value (reference back to t3): {t3}
     *
     *  first type we can check directly, but other 2 we check indirectly by counting the number of recursions, because
     *  if that kind of circular reference happens/exists, we get an infinite loop.
     *
     * @param array $values
     * @param array $calculatedNames
     * @param int   $previousRecursionCount
     *
     * @return array
     * @throws CircularReferenceException
     */
    private function orderCalculatedValues(array $values, array $calculatedNames = [], int $previousRecursionCount = 0): array
    {
        if ($previousRecursionCount >= 1000) {
            throw new CircularReferenceException('Too many Loops, possible self/circular reference');
        }

        $calculated = [];
        $notCalculated = [];
        $notCalculatedNames = []; // holds the names of the variables that we did not manage to calculate values

        foreach ($values as $value) {
            $variables = $this->getNestedVariables($value);

            // if value does not contain variable names
            if (count($variables) === 0) {
                $calculated[] = $value;
                $calculatedNames[] = $value->getName();
            }
            else {
                $variablesInVariable = 0;
                foreach ($variables as $variable) {
                    $variableName = str_replace('{', '', str_replace('}', '', $variable));

                    // first case of circular reference element references himself:
                    if ($value->getName() === $variableName) {
                        throw new CircularReferenceException('Circular reference detected: ' . $value->getName());
                    }

                    if (in_array($variableName, $calculatedNames)) {
                        $variablesInVariable++;
                    }
                }

                /*  if we know all values for all variables the current variable holds reference ex.: t3 = {t1} + {t5}, and
                    both t1 and t5 are know and are saved into $calculatedNames   */
                if ($variablesInVariable === count($variables)) {
                    $calculated[] = $value;
                    $calculatedNames[] = $value->getName();
                }
                else {
                    $notCalculated[] = $value;
                    $notCalculatedNames[] = $variableName;
                }
            }
        }

        // we have still variables that we don't know the values
        if (count($notCalculatedNames) > 0) {
            return $this->orderCalculatedValues($notCalculated, $calculatedNames, ++$previousRecursionCount);
        } else {
            return $calculated;
        }
    }

    /**
     * Return array containing all variables name from the formula
     *
     * Example return for input "{t1} + {t2}":
     * Array (
     *   [0] => {t1}
     *   [1] => {t2}
     * )
     *
     * @param ComputedProductValue $value
     *
     * @return array
     */
    private function getNestedVariables(ComputedProductValue $value)
    {
        $pattern = '/{[^_]*?}/'; // search for {name}, where name does not contain any underscore (_)
        $variables = [];
        preg_match_all($pattern, $value->getFormula(), $variables);

        return $variables[0];
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
