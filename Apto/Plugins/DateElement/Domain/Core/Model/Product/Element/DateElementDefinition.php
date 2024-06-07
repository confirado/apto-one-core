<?php

namespace Apto\Plugins\DateElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementSingleTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use DateTime;
use DateTimeZone;
use Exception;

class DateElementDefinition implements ElementDefinition
{
    const NAME = 'Datum und Uhrzeit';
    const BACKEND_COMPONENT = '<date-element definition-validation="setDefinitionValidation(definitionValidation)"></date-element>';
    const FRONTEND_COMPONENT = '<date-element section-ctrl="$ctrl.section" section="section" element="element"></date-element>';

    /**
     * @var string
     */
    protected $rendering;

    /**
     * @var int
     */
    protected $dateStep;

    /**
     * @var bool
     */
    protected $showDurationInput;

    /**
     * @var ElementValueCollection|null
     */
    protected $durationValues;

    /**
     * @var array
     */
    protected $lockedDates;

    /**
     * @var AptoTranslatedValue
     */
    protected $lockedDatesErrorMessage;

    /**
     * @var AptoTranslatedValue
     */
    protected $valuePrefix;

    /**
     * @var AptoTranslatedValue
     */
    protected $valueSuffix;

    /**
     * @var string
     *
     *  hours | minutes
     */
    protected $unit;

    /**
     * @param string                      $rendering
     * @param int                         $dateStep
     * @param bool                        $showDurationInput
     * @param ElementValueCollection|null $durationValues
     * @param array                       $lockedDates
     * @param AptoTranslatedValue         $lockedDatesErrorMessage
     * @param AptoTranslatedValue         $valuePrefix
     * @param AptoTranslatedValue         $valueSuffix
     * @param string                      $unit
     */
    public function __construct(
        string $rendering,
        int $dateStep,
        bool $showDurationInput,
        ?ElementValueCollection $durationValues,
        array $lockedDates,
        AptoTranslatedValue $lockedDatesErrorMessage,
        AptoTranslatedValue $valuePrefix,
        AptoTranslatedValue $valueSuffix,
        string $unit
    ){
        $this->rendering = $rendering;
        $this->dateStep = $dateStep;
        $this->showDurationInput = $showDurationInput;
        $this->durationValues = $durationValues;
        $this->lockedDates = $lockedDates;
        $this->lockedDatesErrorMessage = $lockedDatesErrorMessage;
        $this->valuePrefix = $valuePrefix;
        $this->valueSuffix = $valueSuffix;
        $this->unit = $unit;
    }

    /**
     * this contains values that can be selected in frontend (inserted from user)
     *
     * @return array
     */
    public function getSelectableValues(): array
    {
        $values = [
            'aptoElementDefinitionId' => new ElementValueCollection([new ElementSingleTextValue('apto-element-date-element')]),
            'date' => new ElementValueCollection([new ElementTextValue(1, 1000)]),
            'year' => new ElementValueCollection([new ElementTextValue(4, 4)]),
            'month' => new ElementValueCollection([new ElementTextValue(1, 2)]),
            'weekDay' => new ElementValueCollection([new ElementTextValue(1, 1)]),
            'day' => new ElementValueCollection([new ElementTextValue(1, 2)]),
            'dayDiff' => new ElementValueCollection([new ElementTextValue(1, 4)])
        ];

        if ($this->showDurationInput) {
            $values['duration'] = $this->durationValues; // this will be validated in handleGetConfigurationState
        }

        if ($this->rendering === 'date-time' ) {
            $values['hour'] = new ElementValueCollection([new ElementTextValue(1, 2)]);
            $values['minute'] = new ElementValueCollection([new ElementTextValue(1, 2)]);
        }

        return $values;
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
     * those are saved in apto_product_element table's "definition" field
     * those are the values that are accessible in frontend
     *
     * @return array
     */
    public function getStaticValues(): array
    {
        return [
            'aptoElementDefinitionId' => 'apto-element-date-element',
            'rendering' => $this->rendering,
            'dateStep' => $this->dateStep,
            'showDurationInput' => $this->showDurationInput,
            'lockedDates' => $this->lockedDates,
            'lockedDatesErrorMessage' => $this->lockedDatesErrorMessage,
            'valuePrefix' => $this->valuePrefix,
            'valueSuffix' => $this->valueSuffix,
            'unit' => $this->unit,
        ];
    }

    /**
     * returns summary for this element
     *
     * for example data from here can be visible in sidebar left, summary page, basket
     *
     * @param array $selectedValues
     * @return array
     * @throws Exception
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        if (!array_key_exists('date', $selectedValues) || !$selectedValues['date']) {
            return [];
        }

        $date = new DateTime($selectedValues['date']);
        $date->setTimezone(new DateTimeZone('Europe/Berlin'));

        if ($this->rendering === 'date-time') {
            $format = 'd.m.Y H:i';
        } else {
            $format = 'd.m.Y';
        }

        $de_DE = new AptoLocale('de_DE');
        $en_GB = new AptoLocale('en_GB');

        $values['date'] = AptoTranslatedValue::fromArray([
            'de_DE' => $date->format($format),
            'en_GB' => $date->format($format)
        ]);

        if (array_key_exists('duration', $selectedValues)){
            $values['duration'] = AptoTranslatedValue::fromArray([
                'de_DE' =>
                    $this->valuePrefix->getTranslation($de_DE, null, true)->getValue() .
                    ' ' . $selectedValues['duration'] . ' ' .
                    $this->valueSuffix->getTranslation($de_DE, null, true)->getValue(),

                'en_GB' =>
                    $this->valuePrefix->getTranslation($en_GB, null, true)->getValue() .
                    ' ' . $selectedValues['duration'] . ' ' .
                    $this->valueSuffix->getTranslation($en_GB, null, true)->getValue(),
            ]);
        }

        return $values;
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
            'json' => [
                'rendering' => $this->rendering,
                'dateStep' => $this->dateStep,
                'showDurationInput' => $this->showDurationInput,
                'duration' => $this->durationValues->jsonEncode(),
                'lockedDates' => $this->lockedDates,
                'lockedDatesErrorMessage' => $this->lockedDatesErrorMessage->jsonSerialize(),
                'valuePrefix' => $this->valuePrefix->jsonSerialize(),
                'valueSuffix' => $this->valueSuffix->jsonSerialize(),
                'unit' => $this->unit,
            ]
        ];
    }

    /**
     * @param array $json
     * @return ElementDefinition
     */
    public static function jsonDecode(array $json): ElementDefinition
    {
        if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'DateElementDefinition\' due to wrong class namespace.');
        }

        if (!isset($json['json']['rendering'])) {
            $json['json']['rendering'] = 'date';
        }

        if (!isset($json['json']['dateStep'])) {
            $json['json']['dateStep'] = 1;
        }

        // set value properties
        if (!isset($json['json']['showDurationInput'])) {
            $json['json']['showDurationInput'] = false;
        }

        if (!isset($json['json']['duration'])) {
            $json['json']['duration'] = null;
        } else {
            $json['json']['duration'] = ElementValueCollection::jsonDecode($json['json']['duration']);
        }

        if (!isset($json['json']['lockedDates'])) {
            $json['json']['lockedDates'] = [];
        }

        if (!isset($json['json']['lockedDatesErrorMessage'])) {
            $json['json']['lockedDatesErrorMessage'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['lockedDatesErrorMessage'] = AptoTranslatedValue::fromArray($json['json']['lockedDatesErrorMessage']);
        }

        if (!isset($json['json']['valuePrefix'])) {
            $json['json']['valuePrefix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['valuePrefix'] = AptoTranslatedValue::fromArray($json['json']['valuePrefix']);
        }

        if (!isset($json['json']['valueSuffix'])) {
            $json['json']['valueSuffix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['valueSuffix'] = AptoTranslatedValue::fromArray($json['json']['valueSuffix']);
        }

        if (!isset($json['json']['unit'])) {
            $json['json']['unit'] = 'hours';
        }

        if ($json['json']['showDurationInput'] && !$json['json']['duration']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'DateElementDefinition\' due to missing values.');
        }

        return new self(
            $json['json']['rendering'],
            $json['json']['dateStep'],
            (bool) $json['json']['showDurationInput'],
            $json['json']['duration'],
            $json['json']['lockedDates'],
            $json['json']['lockedDatesErrorMessage'],
            $json['json']['valuePrefix'],
            $json['json']['valueSuffix'],
            $json['json']['unit'],
        );
    }
}
