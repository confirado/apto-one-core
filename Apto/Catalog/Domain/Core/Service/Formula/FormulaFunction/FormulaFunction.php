<?php

namespace Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction;

use Apto\Catalog\Domain\Core\Service\Formula\Exception\FunctionParserException;

interface FormulaFunction
{

    /**
     * @todo
     * pass MediaFileSystemConnector and other dependencies in constructor!!
     */

    /**
     * Return name of function to search for
     * @return string
     */
    public static function getName(): string;

    /**
     * Replace function call with value
     * @param array $params
     * @param array $variables
     * @return string
     * @throws FunctionParserException
     */
    public function parse(array $params, array $variables = []): string;

}