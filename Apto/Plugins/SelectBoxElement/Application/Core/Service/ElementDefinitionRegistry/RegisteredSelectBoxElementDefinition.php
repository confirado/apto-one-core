<?php

namespace Apto\Plugins\SelectBoxElement\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\Product\Element\SelectBoxElementDefinition;

class RegisteredSelectBoxElementDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return SelectBoxElementDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return SelectBoxElementDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return SelectBoxElementDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return SelectBoxElementDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        if (!isset($definitionValues['multiplierPrefix'])) {
            $definitionValues['multiplierPrefix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['multiplierPrefix'] = AptoTranslatedValue::fromArray($definitionValues['multiplierPrefix']);
        }

        if (!isset($definitionValues['multiplierSuffix'])) {
            $definitionValues['multiplierSuffix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['multiplierSuffix'] = AptoTranslatedValue::fromArray($definitionValues['multiplierSuffix']);
        }

        if (!isset($definitionValues['defaultItem'])) {
            $definitionValues['defaultItem'] = null;
        }
        if (!isset($definitionValues['enableMultiplier'])) {
            $definitionValues['enableMultiplier'] = false;
        }
        if (!isset($definitionValues['enableMultiSelect'])) {
            $definitionValues['enableMultiSelect'] = false;
        }

        return new SelectBoxElementDefinition($definitionValues['multiplierPrefix'], $definitionValues['multiplierSuffix'], $definitionValues['defaultItem'], $definitionValues['enableMultiplier'], $definitionValues['enableMultiSelect']);
    }
}