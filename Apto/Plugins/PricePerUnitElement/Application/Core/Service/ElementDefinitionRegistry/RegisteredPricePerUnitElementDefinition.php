<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\Product\Element\PricePerUnitElementDefinition;

class RegisteredPricePerUnitElementDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return PricePerUnitElementDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return PricePerUnitElementDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return PricePerUnitElementDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return PricePerUnitElementDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        if (!isset($definitionValues['minOne'])) {
            $definitionValues['minOne'] = false;
        }

        if (!isset($definitionValues['textBoxEnabled'])) {
            $definitionValues['textBoxEnabled'] = false;
        }

        if (!isset($definitionValues['textBoxPrefix'])) {
            $definitionValues['textBoxPrefix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['textBoxPrefix'] = AptoTranslatedValue::fromArray($definitionValues['textBoxPrefix']);
        }

        if (!isset($definitionValues['textBoxSuffix'])) {
            $definitionValues['textBoxSuffix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['textBoxSuffix'] = AptoTranslatedValue::fromArray($definitionValues['textBoxSuffix']);
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

        if($definitionValues['selectableValue'] && !array_key_exists('selectableValueType', $definitionValues)) {
            $definitionValues['selectableValueType'] = 'Selectable';
        }

        if (!isset($definitionValues['elementValueRefs'])) {
            $definitionValues['elementValueRefs'] = [];
        }

        /** @phpstan-ignore-next-line  */
        if (!isset($definitionValues['minOne'])) {
            $definitionValues['minOne'] = false;
        }

        $textValues = [];
        if (false === $definitionValues['textBoxEnabled']) {
            $textValues[] = new ElementTextValue();
        } else {
            foreach ($definitionValues['text'] as $textValue) {
                $textValues[] = new ElementTextValue($textValue['minLength'], $textValue['maxLength']);
            }
        }

        return new PricePerUnitElementDefinition(
            (string)$definitionValues['conversionFactor'],
            (bool)$definitionValues['minOne'],
            (bool)$definitionValues['textBoxEnabled'],
            $definitionValues['textBoxPrefix'],
            $definitionValues['textBoxSuffix'],
            $definitionValues['livePricePrefix'],
            $definitionValues['livePriceSuffix'],
            new ElementValueCollection($textValues),
            $definitionValues['elementValueRefs'],
            $definitionValues['sectionId'],
            $definitionValues['elementId'],
            $definitionValues['selectableValue'],
            $definitionValues['selectableValueType']
        );
    }
}
