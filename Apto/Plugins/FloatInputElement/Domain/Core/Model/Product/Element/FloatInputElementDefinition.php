<?php

namespace Apto\Plugins\FloatInputElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionDefaultValues;
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
     * @var string
     */
    protected $renderingType;

    /**
     * @param AptoTranslatedValue $prefix
     * @param AptoTranslatedValue $suffix
     * @param string $defaultValue
     * @param ElementValueCollection $elementValues
     * @param string $conversionFactor
     * @param AptoTranslatedValue $livePricePrefix
     * @param AptoTranslatedValue $livePriceSuffix
     * @param array $elementValueRefs
     * @param string $renderingType
     */
    public function __construct(
        AptoTranslatedValue $prefix,
        AptoTranslatedValue $suffix,
        string $defaultValue,
        ElementValueCollection $elementValues,
        string $conversionFactor,
        AptoTranslatedValue $livePricePrefix,
        AptoTranslatedValue $livePriceSuffix,
        array $elementValueRefs,
        string $renderingType,
    ) {
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->defaultValue = $defaultValue;
        $this->elementValues = $elementValues;
        $this->conversionFactor = $conversionFactor;
        $this->livePricePrefix = $livePricePrefix;
        $this->livePriceSuffix = $livePriceSuffix;
        $this->elementValueRefs = $elementValueRefs;
        $this->renderingType = $renderingType;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        return [
            'value' => $this->elementValues
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
            'conversionFactor' => $this->conversionFactor,
            'livePricePrefix' => $this->livePricePrefix,
            'livePriceSuffix' => $this->livePriceSuffix,
            'elementValueRefs' => $this->elementValueRefs,
            'renderingType' => $this->renderingType ? $this->renderingType : 'input',
        ];
    }

    public function getDefaultValues(): array
    {
        if ($this->defaultValue === '') {
            return [];
        }
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
        $en_GB = new AptoLocale('en_GB');

        return [
            'value' => AptoTranslatedValue::fromArray([
                'de_DE' =>
                    $this->prefix->getTranslation($de_DE, null, true)->getValue() .
                    ' ' . $selectedValues['value'] . ' ' .
                    $this->suffix->getTranslation($de_DE, null, true)->getValue(),
                'en_GB' =>
                    $this->prefix->getTranslation($en_GB, null, true)->getValue() .
                    ' ' . $selectedValues['value'] . ' ' .
                    $this->suffix->getTranslation($en_GB, null, true)->getValue()
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
                'value' => $this->elementValues->jsonEncode(),
                'conversionFactor' => $this->conversionFactor,
                'livePricePrefix' => $this->livePricePrefix->jsonSerialize(),
                'livePriceSuffix' => $this->livePriceSuffix->jsonSerialize(),
                'elementValueRefs' => $this->elementValueRefs,
                'renderingType' => $this->renderingType,
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

        if (!isset($json['json']['renderingType'])) {
            $json['json']['renderingType'] = 'input';
        }

        return new self(
            $json['json']['prefix'],
            $json['json']['suffix'],
            $json['json']['defaultValue'],
            ElementValueCollection::jsonDecode($json['json']['value']),
            $json['json']['conversionFactor'],
            $json['json']['livePricePrefix'],
            $json['json']['livePriceSuffix'],
            $json['json']['elementValueRefs'],
            $json['json']['renderingType'],
        );
    }
}
