<?php

namespace Apto\Plugins\WidthHeightElement\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementRangeValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\WidthHeightElement\Domain\Core\Model\Product\Element\WidthHeightElementDefinition;

class RegisteredWidthHeightElementDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return WidthHeightElementDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return WidthHeightElementDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return WidthHeightElementDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return WidthHeightElementDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     * @throws \Apto\Base\Domain\Core\Model\InvalidTranslatedValueException
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        $widthValues = [];
        $heightValues = [];

        foreach ($definitionValues['width'] as $widthValue) {
            $widthValues[] = new ElementRangeValue($widthValue['minimum'], $widthValue['maximum'], $widthValue['step']);
        }

        foreach ($definitionValues['height'] as $heightValue) {
            $heightValues[] = new ElementRangeValue($heightValue['minimum'], $heightValue['maximum'], $heightValue['step']);
        }

        if (!isset($definitionValues['priceMatrixId'])) {
            $definitionValues['priceMatrixId'] = '';
        }

        $widthValueCollection = new ElementValueCollection($widthValues);
        $heightValueCollection = new ElementValueCollection($heightValues);

        if (!isset($definitionValues['prefixWidth'])) {
            $definitionValues['prefixWidth'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['prefixWidth'] = AptoTranslatedValue::fromArray($definitionValues['prefixWidth']);
        }

        if (!isset($definitionValues['prefixHeight'])) {
            $definitionValues['prefixHeight'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['prefixHeight'] = AptoTranslatedValue::fromArray($definitionValues['prefixHeight']);
        }

        if (!isset($definitionValues['suffixWidth'])) {
            $definitionValues['suffixWidth'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['suffixWidth'] = AptoTranslatedValue::fromArray($definitionValues['suffixWidth']);
        }

        if (!isset($definitionValues['suffixHeight'])) {
            $definitionValues['suffixHeight'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['suffixHeight'] = AptoTranslatedValue::fromArray($definitionValues['suffixHeight']);
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

        if (!isset($definitionValues['renderingWidth'])) {
            $definitionValues['renderingWidth'] = 'input';
        }

        if (!isset($definitionValues['renderingHeight'])) {
            $definitionValues['renderingHeight'] = 'input';
        }

        if (!isset($definitionValues['defaultWidth'])) {
            $definitionValues['defaultWidth'] = null;
        }

        if (!isset($definitionValues['defaultHeight'])) {
            $definitionValues['defaultHeight'] = null;
        }

        if (!isset($definitionValues['renderDialogInOnePageDesktop'])) {
            $definitionValues['renderDialogInOnePageDesktop'] = true;
        }

        return new WidthHeightElementDefinition(
            $widthValueCollection,
            $heightValueCollection,
            $definitionValues['priceMatrixId'],
            $definitionValues['prefixWidth'],
            $definitionValues['prefixHeight'],
            $definitionValues['suffixWidth'],
            $definitionValues['suffixHeight'],
            $definitionValues['livePricePrefix'],
            $definitionValues['livePriceSuffix'],
            $definitionValues['renderingWidth'],
            $definitionValues['renderingHeight'],
            $definitionValues['defaultWidth'],
            $definitionValues['defaultHeight'],
            $definitionValues['renderDialogInOnePageDesktop']
        );
    }
}
