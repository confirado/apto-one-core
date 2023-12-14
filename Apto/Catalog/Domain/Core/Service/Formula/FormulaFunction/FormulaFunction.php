<?php

namespace Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction;

use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

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
     * @param array $aliases
     * @param State|null $state
     * @return string
     */
    public function parse(array $params, array $variables = [], array $aliases = [], ?State $state = null): string;
}
