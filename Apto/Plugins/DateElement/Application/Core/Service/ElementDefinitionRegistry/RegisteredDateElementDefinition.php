<?php

namespace Apto\Plugins\DateElement\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementRangeValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\DateElement\Domain\Core\Model\Product\Element\DateElementDefinition;

class RegisteredDateElementDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return DateElementDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return DateElementDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return DateElementDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return DateElementDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        if (!isset($definitionValues['rendering'])) {
            $definitionValues['rendering'] = 'date';
        }

        if (!isset($definitionValues['dateStep'])) {
            $definitionValues['dateStep'] = 1;
        }

        if (!isset($definitionValues['showDurationInput'])) {
            $definitionValues['showDurationInput'] = false;
        }

        if (!isset($definitionValues['duration'])) {
            $definitionValues['duration'] = null;
        } else {
            $values = [];
            foreach ($definitionValues['duration'] as $value) {
                $values[] = new ElementRangeValue($value['minimum'], $value['maximum'], $value['step']);
            }
            $definitionValues['duration'] = new ElementValueCollection($values);
        }

        if (!isset($definitionValues['lockedDates'])) {
            $definitionValues['lockedDates'] = [];
        }

        if (!isset($definitionValues['lockedDatesErrorMessage'])) {
            $definitionValues['lockedDatesErrorMessage'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['lockedDatesErrorMessage'] = AptoTranslatedValue::fromArray($definitionValues['lockedDatesErrorMessage']);
        }

        if (!isset($definitionValues['valuePrefix'])) {
            $definitionValues['valuePrefix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['valuePrefix'] = AptoTranslatedValue::fromArray($definitionValues['valuePrefix']);
        }

        if (!isset($definitionValues['valueSuffix'])) {
            $definitionValues['valueSuffix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['valueSuffix'] = AptoTranslatedValue::fromArray($definitionValues['valueSuffix']);
        }

        if (!isset($definitionValues['unit'])) {
            $definitionValues['unit'] = 'hours';
        }

        if ($definitionValues['showDurationInput'] && !$definitionValues['duration']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'DateElementDefinition\' due to missing values.');
        }

        return new DateElementDefinition(
            $definitionValues['rendering'],
            $definitionValues['dateStep'],
            (bool) $definitionValues['showDurationInput'],
            $definitionValues['duration'],
            $definitionValues['lockedDates'],
            $definitionValues['lockedDatesErrorMessage'],
            $definitionValues['valuePrefix'],
            $definitionValues['valueSuffix'],
            $definitionValues['unit'],
        );
    }
}
