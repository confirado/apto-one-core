<?php

namespace Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction;

use Apto\Catalog\Domain\Core\Service\Formula\Exception\FunctionParserException;

class Min extends AbstractFormulaFunction
{

    /**
     * @inheritDoc
     */
    public function parse(array $params, array $variables = []): string
    {
        // assert valid param count
        if (count($params) < 1) {
            throw new FunctionParserException(sprintf(
                'Function "%s" expected at least 1 parameter, but %s given.',
                self::getName(),
                count($params)
            ));
        }

        return min(
            self::replaceParams($params, $variables)
        );
    }

}