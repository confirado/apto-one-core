<?php

namespace Apto\Catalog\Domain\Core\Service\Formula;

use Apto\Catalog\Domain\Core\Service\Formula\Exception\VariableParserException;

class VariableParser
{

    /**
     * Parse a formula by replacing all variables
     * @param string $formula
     * @param array $variables associative array, e.g. ['breite' => 200, 'hoehe' => 400]
     * @return string
     * @throws VariableParserException
     */
    public static function parse(string $formula, array $variables): string
    {

        // define callback function
        $callback = function (array $matches) use ($variables): string {
            $name = $matches[1];

            // assert defined variable
            if (!array_key_exists($name, $variables))
            {
                throw new VariableParserException(sprintf(
                    'Variable "%s" not defined.',
                    $name
                ));
            }

            return $variables[$name];
        };

        // parse variables
        return preg_replace_callback('/{(.*?)}/', $callback, $formula);
    }

}