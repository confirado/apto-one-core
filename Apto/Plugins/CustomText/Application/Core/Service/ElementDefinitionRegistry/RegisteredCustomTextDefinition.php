<?php

namespace Apto\Plugins\CustomText\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\CustomText\Domain\Core\Model\Product\Element\CustomTextDefinition;

class RegisteredCustomTextDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return CustomTextDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return CustomTextDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return CustomTextDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return CustomTextDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        $textValues = [];

        foreach ($definitionValues['text'] as $textValue) {
            $textValues[] = new ElementTextValue($textValue['minLength'], $textValue['maxLength']);
        }

        $textValuesCollection = new ElementValueCollection($textValues);

        if (!isset($definitionValues['rendering'])) {
            $definitionValues['rendering'] = 'input';
        }

        if (!isset($definitionValues['placeholder'])) {
            $definitionValues['placeholder'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['placeholder'] = AptoTranslatedValue::fromArray($definitionValues['placeholder']);
        }

        if (!isset($definitionValues['renderDialogInOnePageDesktop'])) {
            $definitionValues['renderDialogInOnePageDesktop'] = true;
        }

        return new CustomTextDefinition(
            $textValuesCollection,
            $definitionValues['rendering'],
            $definitionValues['placeholder'],
            $definitionValues['renderDialogInOnePageDesktop']
        );
    }
}
