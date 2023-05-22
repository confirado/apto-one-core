<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoJsonSerializable;

interface ElementDefinition extends AptoJsonSerializable
{
    /**
     * Returns a collection of ElementValues
     * @return array of ElementValueCollections
     */
    public function getSelectableValues(): array;

    /**
     * Returns a computed value
     * @internal Use internal caching if necessary, e.g. for database related queries / finder
     * @param array $selectedValues like ['width' => 4, 'height' => 5]
     * @return array like ['area' => 20, 'perimeter' => 18]
     */
    public function getComputableValues(array $selectedValues): array;

    /**
     * Returns all static values
     * static values do not depend on values entered by users in FE
     * @return array
     */
    public function getStaticValues(): array;

    /**
     * Returns an array of AptoTranslatedValues
     * @internal Please note: this function can return only a subset of properties returned by getSelectableValues
     * @param array $selectedValues like ['width' => 4, 'height' => 5]
     * @return array like ['width' => new AptoTranslatedValue(['de_DE' => 'Breite 4 mm']), 'height' => new AptoTranslatedValue(['de_DE' => 'Höhe 5 mm'])]
     */
    public function getHumanReadableValues(array $selectedValues): array;

    /**
     * @return string
     */
    public static function getName(): string;

    /**
     * @return string
     */
    public static function getBackendComponent(): string;

    /**
     * @return string
     */
    public static function getFrontendComponent(): string;
}
