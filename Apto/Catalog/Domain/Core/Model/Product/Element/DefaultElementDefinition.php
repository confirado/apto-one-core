<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

class DefaultElementDefinition implements ElementDefinition
{
    const NAME = 'Default';
    const BACKEND_COMPONENT = '<apto-default-element-definition></apto-default-element-definition>';
    const FRONTEND_COMPONENT = '<apto-default-element-definition section-ctrl="$ctrl.section" section="section" element="element"></apto-default-element-definition>';

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        return [];
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
            'aptoElementDefinitionId' => 'apto-element-default-element'
        ];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        return [];
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'DefaultElementDefinition\' due to wrong class namespace.');
        }
        return new self();
    }
}