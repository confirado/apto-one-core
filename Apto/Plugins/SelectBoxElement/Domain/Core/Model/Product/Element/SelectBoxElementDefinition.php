<?php

namespace Apto\Plugins\SelectBoxElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionDefaultValues;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementJsonValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementSingleTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;

class SelectBoxElementDefinition implements ElementDefinition, ElementDefinitionDefaultValues
{
    const NAME = 'SelectBox Element';
    const BACKEND_COMPONENT = '<select-box-element-definition definition-validation="setDefinitionValidation(definitionValidation)" product-id="productId" section-id="sectionId" element="detail"></select-box-element-definition>';
    const FRONTEND_COMPONENT = '<select-box-element-definition section-ctrl="$ctrl.section" section="section" element="element"></select-box-element-definition>';

    /**
     * @var array|null
     */
    private $defaultItem;

    /**
     * @var bool
     */
    private $enableMultiplier;

    /**
     * @var bool
     */
    private $enableMultiSelect;

    /**
     * @var AptoTranslatedValue
     */
    protected $multiplierPrefix;

    /**
     * @var AptoTranslatedValue
     */
    protected $multiplierSuffix;

    /**
     * SelectBoxElementDefinition constructor.
     * @param AptoTranslatedValue $multiplierPrefix
     * @param AptoTranslatedValue $multiplierSuffix
     * @param array|null $defaultItem
     * @param bool $enableMultiplier
     * @param bool $enableMultiSelect
     */
    public function __construct(
        AptoTranslatedValue $multiplierPrefix,
        AptoTranslatedValue $multiplierSuffix,
        array $defaultItem = null,
        bool $enableMultiplier = false,
        bool $enableMultiSelect = false
    ) {
        $this->defaultItem = $defaultItem;
        $this->enableMultiplier = $enableMultiplier;
        $this->multiplierPrefix = $multiplierPrefix;
        $this->multiplierSuffix = $multiplierSuffix;
        $this->enableMultiSelect = $enableMultiSelect;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        return [
            'aptoElementDefinitionId' => new ElementValueCollection([new ElementSingleTextValue('apto-element-select-box')]),
            'boxes' => new ElementValueCollection([new ElementJsonValue()]),
            'selectedItem' => new ElementValueCollection([new ElementJsonValue()])
        ];
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
            'aptoElementDefinitionId' => 'apto-element-select-box',
            'defaultItem' => $this->defaultItem,
            'enableMultiplier' => $this->enableMultiplier,
            'enableMultiSelect' => $this->enableMultiSelect,
            'multiplierPrefix' => $this->multiplierPrefix,
            'multiplierSuffix' => $this->multiplierSuffix
        ];
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        if (!$this->defaultItem) {
            return [];
        }

        return [
            'aptoElementDefinitionId' => 'apto-element-select-box',
            'boxes' => [[
                'id' => $this->defaultItem['id'],
                'multi' => '1',
                'name' => $this->defaultItem['name']
            ]],
            'selectedItem' => [
                $this->defaultItem['id']
            ],
        ];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        // @todo boxes should always be of type array or null, an empty array or null if no selection is done, sometimes it seems to be an empty string
        if (!array_key_exists('boxes', $selectedValues) || !is_array($selectedValues['boxes'])) {
            return [];
        }

        try {
            $de_DE = new AptoLocale('de_DE');
            $en_EN = new AptoLocale('en_EN');
            $humanReadableBoxes = [];

            foreach ($selectedValues['boxes'] as $box) {
                $name = AptoTranslatedValue::fromArray($box['name']);
                $multiplier_de = $this->multiplierPrefix->getTranslation($de_DE, null, true)->getValue() .
                                ' ' . $box['multi'] . ' ' .
                                $this->multiplierSuffix->getTranslation($de_DE, null, true)->getValue() .
                                ' - ';
                $multiplier_en = $this->multiplierPrefix->getTranslation($en_EN, null, true)->getValue() .
                                ' ' . $box['multi'] . ' ' .
                                $this->multiplierSuffix->getTranslation($en_EN, null, true)->getValue() .
                                ' - ';
                $name_de = $name->getTranslation($de_DE, null, true)->getValue();
                $name_en = $name->getTranslation($en_EN, null, true)->getValue();
                if ($this->enableMultiplier) {
                    $box_de = $multiplier_de . $name_de;
                    $box_en = $multiplier_en . $name_en;
                }
                else {
                    $box_de = $name_de;
                    $box_en = $name_en;
                }
                $humanReadableBoxes[] = AptoTranslatedValue::fromArray([
                    'de_DE' =>
                        $box_de,
                    'en_EN' =>
                        $box_en
                ]);
            }

            return $humanReadableBoxes;
        }
        catch (\Exception $e) {
            return [
                'id' => AptoTranslatedValue::fromArray([
                    'de_DE' => 'Id: ' . $selectedValues['id'],
                    'en_EN' => 'Id: ' . $selectedValues['id']
                ])
            ];
        }
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
                'defaultItem' => $this->defaultItem,
                'enableMultiplier' => $this->enableMultiplier,
                'enableMultiSelect' => $this->enableMultiSelect,
                'multiplierPrefix' => $this->multiplierPrefix->jsonSerialize(),
                'multiplierSuffix' => $this->multiplierSuffix->jsonSerialize()
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'SelectBoxElementDefinition\' due to wrong class namespace.');
        }

        if (!isset($json['json']['multiplierPrefix'])) {
            $json['json']['multiplierPrefix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['multiplierPrefix'] = AptoTranslatedValue::fromArray($json['json']['multiplierPrefix']);
        }

        if (!isset($json['json']['multiplierSuffix'])) {
            $json['json']['multiplierSuffix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['multiplierSuffix'] = AptoTranslatedValue::fromArray($json['json']['multiplierSuffix']);
        }

        if (!isset($json['json']['defaultItem'])) {
            $json['json']['defaultItem'] = null;
        }
        if (!isset($json['json']['enableMultiplier'])) {
            $json['json']['enableMultiplier'] = false;
        }
        if (!isset($json['json']['enableMultiSelect'])) {
            $json['json']['enableMultiSelect'] = false;
        }

        return new self($json['json']['multiplierPrefix'], $json['json']['multiplierSuffix'], $json['json']['defaultItem'], $json['json']['enableMultiplier'], $json['json']['enableMultiSelect']);
    }
}
