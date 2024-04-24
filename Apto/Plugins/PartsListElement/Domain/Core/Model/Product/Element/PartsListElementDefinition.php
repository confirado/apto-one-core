<?php

namespace Apto\Plugins\PartsListElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementSingleTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Catalog\Domain\Core\Model\Product\Element\InvalidSelectablePropertyException;

class PartsListElementDefinition implements ElementDefinition
{
    const NAME = 'Parts List Element';
    const BACKEND_COMPONENT = '<parts-list-element definition-validation="setDefinitionValidation(definitionValidation)"></parts-list-element>';
    const FRONTEND_COMPONENT = '';

    /**
     * @return array
     * @throws InvalidSelectablePropertyException
     */
    public function getSelectableValues(): array
    {
        return [
            'aptoElementDefinitionId' => new ElementValueCollection([new ElementSingleTextValue('apto-parts-list-element')]),
        ];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getComputableValues(array $selectedValues): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getStaticValues(): array
    {
        return [
            'aptoElementDefinitionId' => 'apto-parts-list-element',
        ];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        return [
            'text' => AptoTranslatedValue::fromArray([
                'de_DE' => $selectedValues['text'],
                'en_EN' => $selectedValues['text']
            ])
        ];
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public static function getBackendComponent(): string
    {
        return self::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public static function getFrontendComponent(): string
    {
        return self::FRONTEND_COMPONENT;
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
            'json' => []
        ];
    }

    /**
     * @param array $json
     * @return ElementDefinition
     */
    public static function jsonDecode(array $json): ElementDefinition
    {
        if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'PartsListElementDefinition\' due to wrong class namespace.');
        }

        return new self();
    }
}
