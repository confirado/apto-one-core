<?php

namespace Apto\Plugins\FloatInputElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionDefaultValues;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementSingleTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;

class FloatInputElementDefinition implements ElementDefinition, ElementDefinitionDefaultValues
{
    const NAME = 'Fließkommazahl Eingabe Element';
    const BACKEND_COMPONENT = '<float-input-element-definition definition-validation="setDefinitionValidation(definitionValidation)" product-id="productId" section-id="sectionId" element="detail"></float-input-element-definition>';
    const FRONTEND_COMPONENT = '<float-input-element-definition section-ctrl="$ctrl.section" section="section" element="element"></float-input-element-definition>';

    /**
     * @var AptoTranslatedValue
     */
    protected $prefix;

    /**
     * @var AptoTranslatedValue
     */
    protected $suffix;

    /**
     * @var string
     */
    protected $defaultValue;

    /**
     * @var bool
     */
    protected $useDefaultValue;

    /**
     * @var bool
     */
    protected $showDefaultValue;

    /**
     * @var ElementValueCollection
     */
    protected $elementValues;

    /**
     * @var string
     */
    protected $conversionFactor;

    /**
     * @var AptoTranslatedValue
     */
    protected $livePricePrefix;

    /**
     * @var AptoTranslatedValue
     */
    protected $livePriceSuffix;

    /**
     * @var array
     */
    protected $elementValueRefs;

    /**
     * FloatInputElementDefinition constructor.
     * @param AptoTranslatedValue $prefix
     * @param AptoTranslatedValue $suffix
     * @param string $defaultValue
     * @param bool $useDefaultValue
     * @param bool $showDefaultValue
     * @param ElementValueCollection $elementValues
     * @param string $conversionFactor
     * @param AptoTranslatedValue $livePricePrefix
     * @param AptoTranslatedValue $livePriceSuffix
     * @param array $elementValueRefs
     */
    public function __construct(
        AptoTranslatedValue $prefix,
        AptoTranslatedValue $suffix,
        string $defaultValue,
        bool $useDefaultValue,
        bool $showDefaultValue,
        ElementValueCollection $elementValues,
        string $conversionFactor,
        AptoTranslatedValue $livePricePrefix,
        AptoTranslatedValue $livePriceSuffix,
        array $elementValueRefs
    ) {
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->defaultValue = $defaultValue;
        $this->useDefaultValue = $useDefaultValue;
        $this->showDefaultValue = $showDefaultValue;
        $this->elementValues = $elementValues;
        $this->conversionFactor = $conversionFactor;
        $this->livePricePrefix = $livePricePrefix;
        $this->livePriceSuffix = $livePriceSuffix;
        $this->elementValueRefs = $elementValueRefs;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        // add default value to collection of valid values, if defined and not already contained
        if ($this->useDefaultValue && !$this->elementValues->contains($this->defaultValue)) {
            $value = $this->elementValues->getCollection();
            $value[] = new ElementSingleTextValue($this->defaultValue);
            $value = new ElementValueCollection($value);
        } else {
            $value = $this->elementValues;
        }

        return [
            'value' => $value
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
            'aptoElementDefinitionId' => 'apto-element-float-input-element',
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'defaultValue' => $this->defaultValue,
            'useDefaultValue' => $this->useDefaultValue,
            'showDefaultValue' => $this->showDefaultValue,
            'conversionFactor' => $this->conversionFactor,
            'livePricePrefix' => $this->livePricePrefix,
            'livePriceSuffix' => $this->livePriceSuffix,
            'elementValueRefs' => $this->elementValueRefs
        ];
    }

    public function getDefaultValues(): array
    {
        return [
            'value' => (float) $this->defaultValue
        ];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        $de_DE = new AptoLocale('de_DE');
        $en_EN = new AptoLocale('en_EN');

        return [
            'value' => AptoTranslatedValue::fromArray([
                'de_DE' =>
                    $this->prefix->getTranslation($de_DE, null, true)->getValue() .
                    ' ' . $selectedValues['value'] . ' ' .
                    $this->suffix->getTranslation($de_DE, null, true)->getValue(),
                'en_EN' =>
                    $this->prefix->getTranslation($en_EN, null, true)->getValue() .
                    ' ' . $selectedValues['value'] . ' ' .
                    $this->suffix->getTranslation($en_EN, null, true)->getValue()
            ])
        ];
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
                'prefix' => $this->prefix->jsonSerialize(),
                'suffix' => $this->suffix->jsonSerialize(),
                'defaultValue' => $this->defaultValue,
                'useDefaultValue' => $this->useDefaultValue,
                'showDefaultValue' => $this->showDefaultValue,
                'value' => $this->elementValues->jsonEncode(),
                'conversionFactor' => $this->conversionFactor,
                'livePricePrefix' => $this->livePricePrefix->jsonSerialize(),
                'livePriceSuffix' => $this->livePriceSuffix->jsonSerialize(),
                'elementValueRefs' => $this->elementValueRefs
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'FloatInputElementDefinition\' due to wrong class namespace.');
        }
        if (
            !isset($json['json']['value'])
        ) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'FloatInputElementDefinition\' due to missing values.');
        }

        if (!isset($json['json']['prefix'])) {
            $json['json']['prefix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['prefix'] = AptoTranslatedValue::fromArray($json['json']['prefix']);
        }

        if (!isset($json['json']['suffix'])) {
            $json['json']['suffix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['suffix'] = AptoTranslatedValue::fromArray($json['json']['suffix']);
        }

        if (!isset($json['json']['defaultValue'])) {
            $json['json']['defaultValue'] = '';
        }

        if (!isset($json['json']['useDefaultValue'])) {
            $json['json']['useDefaultValue'] = false;
        }

        if (!isset($json['json']['showDefaultValue'])) {
            $json['json']['showDefaultValue'] = false;
        }

        if (!isset($json['json']['conversionFactor'])) {
            $json['json']['conversionFactor'] = '1';
        }

        if (!isset($json['json']['livePricePrefix'])) {
            $json['json']['livePricePrefix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['livePricePrefix'] = AptoTranslatedValue::fromArray($json['json']['livePricePrefix']);
        }

        if (!isset($json['json']['livePriceSuffix'])) {
            $json['json']['livePriceSuffix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['livePriceSuffix'] = AptoTranslatedValue::fromArray($json['json']['livePriceSuffix']);
        }

        if (!isset($json['json']['elementValueRefs'])) {
            $json['json']['elementValueRefs'] = [];
        }

        return new self(
            $json['json']['prefix'],
            $json['json']['suffix'],
            $json['json']['defaultValue'],
            (bool)$json['json']['useDefaultValue'],
            (bool)$json['json']['showDefaultValue'],
            ElementValueCollection::jsonDecode($json['json']['value']),
            $json['json']['conversionFactor'],
            $json['json']['livePricePrefix'],
            $json['json']['livePriceSuffix'],
            $json['json']['elementValueRefs']
        );
    }
}
