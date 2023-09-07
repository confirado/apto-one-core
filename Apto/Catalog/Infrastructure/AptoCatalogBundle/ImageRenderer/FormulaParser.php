<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\ImageRenderer;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Catalog\Application\Core\Query\Product\Element\ProductElementFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use MathParser\Exceptions\UnknownVariableException;

trait FormulaParser
{

    /**
     * @var ProductElementFinder
     */
    protected $productElementFinder;

    /**
     * @var AptoJsonSerializer
     */
    protected $aptoJsonSerializer;

    /**
     * List of all characters, that are allowed as variables
     * @var string
     */
    protected static $formulaChars = 'abcdfghijklmnopqrstuvwxyz';


    /**
     * Return result of formula or null, if invalid or empty formula
     * @param null|string $formula
     * @param array $aliases
     * @return null|string
     */
    protected function calculateFormula(?string $formula, array $aliases): ?string
    {
        if (!$formula) {
            return null;
        }

        // get mappings
        list('aliasesToChars' => $variablesToChars, 'charsToValues' => $charsToValues) = $this->mapAliasesToChars($aliases);

        // replace aliases with chars
        $formula = str_replace(
            array_keys($variablesToChars),
            array_values($variablesToChars),
            $formula
        );

        try {
            // insert values for chars
            return (string) math_eval($formula, $charsToValues);
        } catch (UnknownVariableException $e) {
            return null;
        }
    }

    /**
     * @param array $aliases
     * @return array
     */
    private static function mapAliasesToChars(array $aliases): array
    {
        $availableChars = str_split(self::$formulaChars);
        $aliasesToChars = [];
        $charsToValues = [];
        $i = 0;

        // todo check mapping!
        foreach ($aliases as $alias => $value) {
            $char = $availableChars[$i++];
            $aliasesToChars[$alias] = $char;
            $charsToValues[$char] = $value;
        }

        return [
            'aliasesToChars' => $aliasesToChars,
            'charsToValues' => $charsToValues
        ];
    }

    /**
     * Get formula aliases from given options
     * @param State $state
     * @param array $options
     * @return array
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    private function getFormulaAliases(State $state, array $options): array
    {
        $aliases = [];

        foreach ($options['elementValueRefs'] as $elementValueRef) {

            switch ($elementValueRef['selectableValueType']) {

                // get value from selectable
                case 'Selectable': {
                    $stateValue = $state->getValue(
                        new AptoUuid($elementValueRef['sectionId']),
                        new AptoUuid($elementValueRef['elementId']),
                        $elementValueRef['selectableValue']
                    );
                    if (null !== $stateValue) {
                        $aliases[$elementValueRef['alias']] = $stateValue;
                    }
                    break;
                }

                // get value from computable
                case 'Computable': {
                    $stateValue = $this->getComputableValue(
                        $state,
                        $elementValueRef['sectionId'],
                        $elementValueRef['elementId'],
                        $elementValueRef['selectableValue']
                    );
                    if ($stateValue !== null) {
                        $aliases[$elementValueRef['alias']] = $stateValue;
                    }
                    break;
                }

                // unknown value type
                default: {
                    throw new \InvalidArgumentException(sprintf(
                        'Unknown value type "%s".',
                        $elementValueRef['selectableValueType']
                    ));
                }
            }
        }

        return $aliases;
    }


    /**
     * @param State $state
     * @param string $sectionId
     * @param string $elementId
     * @param string $selectableValue
     * @return string|null
     * @throws AptoJsonSerializerException
     */
    private function getComputableValue(State $state, string $sectionId, string $elementId, string $selectableValue): ?string
    {
        $stateArray = $state->getStateWithoutParameters();

        if (
            !array_key_exists($sectionId, $stateArray) ||
            !array_key_exists($elementId, $stateArray[$sectionId]) ||
            !is_array($stateArray[$sectionId][$elementId])
        ) {
            return null;
        }

        $element = $this->productElementFinder->findById($elementId);
        /** @var ElementDefinition $elementDefinition */
        $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize(json_encode($element['definition'], JSON_UNESCAPED_UNICODE));
        $computableValues = $elementDefinition->getComputableValues($stateArray[$sectionId][$elementId]);

        if (!array_key_exists($selectableValue, $computableValues)) {
            return null;
        }

        return (string) $computableValues[$selectableValue];
    }

}
