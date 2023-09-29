<?php

namespace Apto\Plugins\FloatInputElement\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementRangeValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\Product\Element\FloatInputElementDefinition;

class RegisteredFloatInputElementDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return FloatInputElementDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return FloatInputElementDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return FloatInputElementDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return FloatInputElementDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        $values = [];
        foreach ($definitionValues['value'] as $value) {
            $values[] = new ElementRangeValue($value['minimum'], $value['maximum'], $value['step']);
        }

        if (!isset($definitionValues['prefix'])) {
            $definitionValues['prefix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['prefix'] = AptoTranslatedValue::fromArray($definitionValues['prefix']);
        }

        if (!isset($definitionValues['suffix'])) {
            $definitionValues['suffix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['suffix'] = AptoTranslatedValue::fromArray($definitionValues['suffix']);
        }

        if (!isset($definitionValues['defaultValue'])) {
            $definitionValues['defaultValue'] = '';
        }

        if (!isset($definitionValues['conversionFactor'])) {
            $definitionValues['conversionFactor'] = '1';
        }

        if (!isset($definitionValues['livePricePrefix'])) {
            $definitionValues['livePricePrefix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['livePricePrefix'] = AptoTranslatedValue::fromArray($definitionValues['livePricePrefix']);
        }

        if (!isset($definitionValues['livePriceSuffix'])) {
            $definitionValues['livePriceSuffix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['livePriceSuffix'] = AptoTranslatedValue::fromArray($definitionValues['livePriceSuffix']);
        }

        if (!isset($definitionValues['elementValueRefs'])) {
            $definitionValues['elementValueRefs'] = [];
        }

        if (!isset($definitionValues['renderingType'])) {
            $definitionValues['renderingType'] = 'input';
        }

        return new FloatInputElementDefinition(
            $definitionValues['prefix'],
            $definitionValues['suffix'],
            $definitionValues['defaultValue'],
            new ElementValueCollection($values),
            $definitionValues['conversionFactor'],
            $definitionValues['livePricePrefix'],
            $definitionValues['livePriceSuffix'],
            $definitionValues['elementValueRefs'],
            $definitionValues['renderingType'],
        );
    }
}
