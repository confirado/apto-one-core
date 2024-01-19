<?php

namespace Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Service\Formula\Exception\FunctionParserException;

class Repetition extends AbstractFormulaFunction
{
    /**
     * @param array $params
     * @param array $variables
     * @param array $aliases
     * @param State|null $state
     * @return string
     * @throws FunctionParserException
     * @throws InvalidUuidException
     */
    public function parse(array $params, array $variables = [], array $aliases = [], ?State $state = null): string
    {
        // assert valid param count
        if (count($params) < 2) {
            throw new FunctionParserException(sprintf(
                'Function "%s" expected at least 2 parameter, but %s given.',
                self::getName(),
                count($params)
            ));
        }
        $alias = $params[0];
        $func = $params[1];

        // assert valid given alias
        if (!array_key_exists($alias, $aliases)) {
            throw new FunctionParserException(sprintf(
                'Alias "%s" not found.',
                $alias
            ));
        }

        // assert valid given alias
        if ($aliases[$alias]['isCustomProperty'] === true) {
            throw new FunctionParserException(sprintf(
                'Alias "%s" can not be a custom Property found.',
                $alias
            ));
        }

        // calculate alias value for all repetitions
        $value = 0;
        $property = $aliases[$alias]['property'];
        $repetitions = $state->getElementRepetitions(new AptoUuid($aliases[$alias]['sectionId']), new AptoUuid($aliases[$alias]['elementId']));
        foreach ($repetitions as $repetition) {
            switch ($func) {
                case 'add': {
                    $value = $this->add($value, $repetition, $property);
                    break;
                }
            }
        }

        // return value
        return (string) $value;
    }

    /**
     * @param float $value
     * @param array $repetition
     * @param string $property
     * @return float
     */
    private function add(float $value, array $repetition, string $property): float
    {
        if (!$property) {
            return ++$value;
        }

        if (array_key_exists('values', $repetition) || array_key_exists($property, $repetition['values'])) {
            return $value + (float) $repetition['values'][$property];
        }

        return 0;
    }
}
