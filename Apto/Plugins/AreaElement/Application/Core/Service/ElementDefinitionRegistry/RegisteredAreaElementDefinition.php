<?php

namespace Apto\Plugins\AreaElement\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementRangeValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\AreaElement\Domain\Core\Model\Product\Element\AreaElementDefinition;

class RegisteredAreaElementDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return AreaElementDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return AreaElementDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return AreaElementDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return AreaElementDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     * @throws \Apto\Base\Domain\Core\Model\InvalidTranslatedValueException
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        // set defaults if not set already
        if (!isset($definitionValues['renderDialogInOnePageDesktop'])) {
            $definitionValues['renderDialogInOnePageDesktop'] = true;
        }

        if (!isset($definitionValues['priceMatrix'])) {
            $definitionValues['priceMatrix'] = [
                'id' => null,
                'row' => null,
                'column' => null
            ];
        }

        if (!isset($definitionValues['fields'])) {
            $definitionValues['fields'] = [[
                'prefix' => [],
                'suffix' => [],
                'rendering' => 'input',
                'default' => null,
                'values' => []
            ]];
        }

        // set fields
        foreach ($definitionValues['fields'] as &$field) {
            // set prefix
            if (!isset($field['prefix'])) {
                $field['prefix'] = new AptoTranslatedValue([]);
            } else {
                $field['prefix'] = AptoTranslatedValue::fromArray($field['prefix']);
            }

            // set suffix
            if (!isset($field['suffix'])) {
                $field['suffix'] = new AptoTranslatedValue([]);
            } else {
                $field['suffix'] = AptoTranslatedValue::fromArray($field['suffix']);
            }

            // set rendering
            if (!isset($field['rendering'])) {
                $field['rendering'] = 'input';
            }

            // set default
            if (!isset($field['default'])) {
                $field['default'] = null;
            }

            // set values
            foreach ($field['values'] as &$value) {
                $value = new ElementRangeValue($value['minimum'], $value['maximum'], $value['step']);
            }

            $field['values'] = new ElementValueCollection($field['values']);
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

        if (isset($definitionValues['sumOfFieldValues']) && count($definitionValues['sumOfFieldValues']) > 0) {
            $sumOfFieldValues = [];
            foreach ($definitionValues['sumOfFieldValues'] as $sumOfFieldValues) {
                $sumOfFieldValues[] = new ElementRangeValue($sumOfFieldValues['minimum'], $sumOfFieldValues['maximum'], $sumOfFieldValues['step']);
            }
            $sumOfFieldValuesCollection = new ElementValueCollection($sumOfFieldValues);
        } else {
            $sumOfFieldValuesCollection = null;
        }

        $priceMultiplication = [];
        if (isset($definitionValues['priceMultiplication'])) {
            $priceMultiplication = $definitionValues['priceMultiplication'];
        }

        return new AreaElementDefinition(
            $definitionValues['renderDialogInOnePageDesktop'],
            $definitionValues['priceMatrix'],
            $definitionValues['fields'],
            $definitionValues['livePricePrefix'],
            $definitionValues['livePriceSuffix'],
            $sumOfFieldValuesCollection,
            $priceMultiplication
        );
    }
}