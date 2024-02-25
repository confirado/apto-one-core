<?php

namespace Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction;

abstract class AbstractFormulaFunction implements FormulaFunction
{

    /**
     * @inheritDoc
     */
    final public static function getName(): string
    {
        $reflectionClass = new \ReflectionClass(static::class);
        return lcfirst(
            $reflectionClass->getShortName()
        );
    }

    /**
     * Replace all params with corresponding variable values, leave undefined params untouched
     * @param array $params
     * @param array $variables
     * @return array
     */
    protected static function replaceParams(array $params, array $variables): array
    {
        $values = [];
        foreach ($params as $param) {
            $values[] = self::replaceParam($param, $variables);
        }

        return $values;
    }

    /**
     * Replace given param with corresponding variable value, leave undefined param untouched
     * @param string $param
     * @param array $variables
     * @return string
     */
    protected static function replaceParam(string $param, array $variables): string
    {
        if (array_key_exists($param, $variables)) {
            return $variables[$param];
        }

        return $param;
    }

}
