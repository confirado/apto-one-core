<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Product\Element\MaterialPickerElementDefinition;

class RegisteredMaterialPickerElementDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return MaterialPickerElementDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return MaterialPickerElementDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return MaterialPickerElementDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return MaterialPickerElementDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        if (!isset($definitionValues['poolId'])) {
            $definitionValues['poolId'] = '';
        }

        if (!isset($definitionValues['defaultMaterialId'])) {
            $definitionValues['defaultMaterialId'] = '';
        }

        if (!isset($definitionValues['defaultMaterialPoolId'])) {
            $definitionValues['defaultMaterialPoolId'] = '';
        }

        if (!isset($definitionValues['secondaryMaterialActive'])) {
            $definitionValues['secondaryMaterialActive'] = false;
        }

        if (!array_key_exists('secondaryMaterialAdditionalCharge', $definitionValues)) {
            $definitionValues['secondaryMaterialAdditionalCharge'] = 1500;
        }

        if (!isset($definitionValues['monochromeImage'])) {
            $definitionValues['monochromeImage'] = '';
        }

        if (!isset($definitionValues['multicoloredImageAlternately'])) {
            $definitionValues['multicoloredImageAlternately'] = '';
        }

        if (!isset($definitionValues['multicoloredImageInput'])) {
            $definitionValues['multicoloredImageInput'] = '';
        }

        if (!isset($definitionValues['searchboxActive'])) {
            $definitionValues['searchboxActive'] = false;
        }

        if (!isset($definitionValues['allowMultiple'])) {
            $definitionValues['allowMultiple'] = false;
        }

        if (!isset($definitionValues['altColorSelect'])) {
            $definitionValues['altColorSelect'] = false;
        }

        if (!isset($definitionValues['colorSectionActive'])) {
            $definitionValues['colorSectionActive'] = true;
        }

        if (!isset($definitionValues['priceGroupActive'])) {
            $definitionValues['priceGroupActive'] = true;
        }

        /*  In the pas we had a checkbox for sortByPosition:
            true/checked was sort by position
            false/uncheck was the sort by clicks.

            now we have selectbox instead of checkbox with 3 possible values:
            'clicks', 'position', 'pricegroup'. */
        if (!isset($definitionValues['sortByPosition'])) {
            $definitionValues['sortByPosition'] = 'clicks';
        }
        else {
            if ($definitionValues['sortByPosition'] === true) {
                $definitionValues['sortByPosition'] = 'position';
            }
            else if ($definitionValues['sortByPosition'] === false) {
                $definitionValues['sortByPosition'] = 'clicks';
            }
        }

        if (!isset($definitionValues['showPriceGroupInMaterialName'])) {
            $definitionValues['showPriceGroupInMaterialName'] = false;
        }

        if (!isset($definitionValues['sortOrderActive'])) {
            $definitionValues['sortOrderActive'] = false;
        }

        return new MaterialPickerElementDefinition(
            $definitionValues['poolId'],
            $definitionValues['defaultMaterialId'],
            $definitionValues['defaultMaterialPoolId'],
            $definitionValues['secondaryMaterialActive'],
            $definitionValues['secondaryMaterialAdditionalCharge'],
            $definitionValues['monochromeImage'],
            $definitionValues['multicoloredImageAlternately'],
            $definitionValues['multicoloredImageInput'],
            $definitionValues['searchboxActive'],
            $definitionValues['allowMultiple'],
            $definitionValues['altColorSelect'],
            $definitionValues['colorSectionActive'],
            $definitionValues['priceGroupActive'],
            $definitionValues['sortByPosition'],
            $definitionValues['showPriceGroupInMaterialName'],
            $definitionValues['sortOrderActive']
        );
    }
}
