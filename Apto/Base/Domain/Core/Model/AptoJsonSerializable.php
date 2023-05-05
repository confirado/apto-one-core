<?php

namespace Apto\Base\Domain\Core\Model;

interface AptoJsonSerializable
{
    /**
     * Must return an array of the following structure
     * [
     *     'class' => 'Namespace\SubNamespace\Class',
     *     'json' => [
     *         'someProperty' => 'someValue',
     *         'anotherProperty' => 'anotherValue',
     *         ...
     *     ]
     * ]
     * @return array
     */
    public function jsonEncode(): array;

    /**
     * @param array $json
     */
    public static function jsonDecode(array $json);

}