<?php

namespace Apto\Catalog\Domain\Core\Service\Formula;

use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;

class FormulaParser
{

    /**
     * @param string $formula
     * @param array $variables
     * @param MediaFileSystemConnector|null $fileSystemConnector
     * @return string
     * @throws Exception\FormulaParserException
     */
    public static function parse(string $formula, array $variables = [], ?MediaFileSystemConnector $fileSystemConnector = null): string
    {
        // parse variables
        $formula = VariableParser::parse(
            $formula,
            $variables
        );

        // parse functions
        $formula = FunctionParser::parse(
            $formula,
            $variables,
            $fileSystemConnector
        );

        return math_eval($formula);
    }

}
