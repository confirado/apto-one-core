<?php

namespace Apto\Catalog\Domain\Core\Service\Formula;

use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Catalog\Domain\Core\Service\Formula\Exception\FunctionParserException;
use Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction\FormulaFunction;
use Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction\Matrix;
use Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction\Max;
use Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction\Min;

class FunctionParser
{

    /**
     * Parse a formula by replacing all custom function calls
     * @param string $formula
     * @param array $variables
     * @param MediaFileSystemConnector|null $fileSystemConnector
     * @return string
     * @throws FunctionParserException
     */
    public static function parse(string $formula, array $variables, ?MediaFileSystemConnector $fileSystemConnector = null): string
    {
        $formulaFunctions = [
            new Min(),
            new Max(),
            new Matrix($fileSystemConnector)
        ];

        /** @var FormulaFunction $formulaFunction */
        foreach ($formulaFunctions as $formulaFunction) {
            $formula = self::parseFunction($formula, $variables, $formulaFunction);
        }

        return $formula;
    }

    /**
     * Replace all occurrences of formulaFunction within formula
     * @param string $formula
     * @param array $variables
     * @param FormulaFunction $formulaFunction
     * @return string
     * @throws FunctionParserException
     */
    protected static function parseFunction(string $formula, array $variables, FormulaFunction $formulaFunction): string
    {
        // define callback function
        $callback = function (array $matches) use ($variables, $formulaFunction): string {
            $params = trim($matches[1]);

            // split params and trim spaces from all params
            $params = array_map(
                'trim',
                $params ? explode(',', $params) : []
            );

            // call formula specific parse function with params
            return $formulaFunction->parse($params, $variables);
        };

        // get escaped function name
        $escapedName = preg_quote($formulaFunction::getName(), '/');

        // parse variables
        return preg_replace_callback(
            '/' . $escapedName . '\((.*?)\)/',
            $callback,
            $formula
        );
    }

}