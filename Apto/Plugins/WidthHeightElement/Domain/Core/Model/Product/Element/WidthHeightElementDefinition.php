<?php

namespace Apto\Plugins\WidthHeightElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionDefaultValues;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementSingleTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;

class WidthHeightElementDefinition implements ElementDefinition, ElementDefinitionDefaultValues
{
    const NAME = 'Höhe Breite Element';
    const BACKEND_COMPONENT = '<apto-width-height-element definition-validation="setDefinitionValidation(definitionValidation)"></apto-width-height-element>';
    const FRONTEND_COMPONENT = '<apto-width-height-element section-ctrl="$ctrl.section" section="section" element="element"></apto-width-height-element>';

    /**
     * @var ElementValueCollection
     */
    protected $elementWidthValues;

    /**
     * @var ElementValueCollection
     */
    protected $elementHeightValues;

    /**
     * @var string
     */
    protected $priceMatrixId;

    /**
     * @var AptoTranslatedValue
     */
    protected $prefixWidth;

    /**
     * @var AptoTranslatedValue
     */
    protected $prefixHeight;

    /**
     * @var AptoTranslatedValue
     */
    protected $suffixWidth;

    /**
     * @var AptoTranslatedValue
     */
    protected $suffixHeight;

    /**
     * @var AptoTranslatedValue
     */
    protected $livePricePrefix;

    /**
     * @var AptoTranslatedValue
     */
    protected $livePriceSuffix;

    /**
     * @var string
     */
    protected $renderingWidth;

    /**
     * @var string
     */
    protected $renderingHeight;

    /**
     * @var string|null
     */
    protected $defaultWidth;

    /**
     * @var string|null
     */
    protected $defaultHeight;

    /**
     * @var boolean
     */
    protected $renderDialogInOnePageDesktop;

    /**
     * WidthHeightElementDefinition constructor.
     * @param ElementValueCollection $elementWidthValues
     * @param ElementValueCollection $elementHeightValues
     * @param string $priceMatrixId
     * @param AptoTranslatedValue $prefixWidth
     * @param AptoTranslatedValue $prefixHeight
     * @param AptoTranslatedValue $suffixWidth
     * @param AptoTranslatedValue $suffixHeight
     * @param AptoTranslatedValue $livePricePrefix
     * @param AptoTranslatedValue $livePriceSuffix
     * @param string $renderingWidth
     * @param string $renderingHeight
     * @param string|null $defaultWidth
     * @param string|null $defaultHeight
     * @param bool $renderDialogInOnePageDesktop
     */
    public function __construct(
        ElementValueCollection $elementWidthValues,
        ElementValueCollection $elementHeightValues,
        string $priceMatrixId,
        AptoTranslatedValue $prefixWidth,
        AptoTranslatedValue $prefixHeight,
        AptoTranslatedValue $suffixWidth,
        AptoTranslatedValue $suffixHeight,
        AptoTranslatedValue $livePricePrefix,
        AptoTranslatedValue $livePriceSuffix,
        string $renderingWidth,
        string $renderingHeight,
        string $defaultWidth = null,
        string $defaultHeight = null,
        bool $renderDialogInOnePageDesktop = true
    ) {
        if(!$defaultWidth && '0' !== $defaultWidth) {
            $defaultWidth = null;
        }

        if(!$defaultHeight && '0' !== $defaultHeight) {
            $defaultHeight = null;
        }

        if(null !== $defaultWidth && !$elementWidthValues->contains($defaultWidth)) {
            throw new \InvalidArgumentException('Default width value \'' . $defaultWidth . '\' is not a part of width values.');
        }

        if(null !== $defaultHeight && !$elementHeightValues->contains($defaultHeight)) {
            throw new \InvalidArgumentException('Default height value \'' . $defaultHeight . '\' is not a part of height values.');
        }

        $this->elementWidthValues = $elementWidthValues;
        $this->elementHeightValues = $elementHeightValues;

        $this->priceMatrixId = $priceMatrixId;

        $this->prefixWidth = $prefixWidth;
        $this->prefixHeight = $prefixHeight;

        $this->suffixWidth = $suffixWidth;
        $this->suffixHeight = $suffixHeight;

        $this->livePricePrefix = $livePricePrefix;
        $this->livePriceSuffix = $livePriceSuffix;

        $this->renderingWidth = $renderingWidth;
        $this->renderingHeight = $renderingHeight;

        $this->defaultWidth = $defaultWidth;
        $this->defaultHeight = $defaultHeight;

        $this->renderDialogInOnePageDesktop = $renderDialogInOnePageDesktop;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        $values = [
            'aptoElementDefinitionId' => new ElementValueCollection([new ElementSingleTextValue('apto-element-width-height')])
        ];

        if ($this->renderingWidth !== 'none') {
            $values['width'] = $this->elementWidthValues;
        }
        if ($this->renderingHeight !== 'none') {
            $values['height'] = $this->elementHeightValues;
        }

        return $values;
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getComputableValues(array $selectedValues): array
    {
        if (!isset($selectedValues['width']) || !isset($selectedValues['height'])) {
            return [];
        }
        return [
            'area' => $selectedValues['width'] * $selectedValues['height'],
            'perimeter' => 2 * ($selectedValues['width'] + $selectedValues['height'])
        ];
    }

    /**
     * @return array
     */
    public function getStaticValues(): array
    {
        return [
            'aptoElementDefinitionId' => 'apto-element-width-height',
            'priceMatrix' => [
                'id' => $this->priceMatrixId,
                'prices' => [
                    'window' => [
                        'operation' => 'add', // operation "add" is default, just for code readability
                        'columnProperty' => 'width',
                        'rowProperty' => 'height'
                    ]
                ]
            ],
            'prefixWidth' => $this->prefixWidth,
            'prefixHeight' => $this->prefixHeight,
            'suffixWidth' => $this->suffixWidth,
            'suffixHeight' => $this->suffixHeight,
            'livePricePrefix' => $this->livePricePrefix,
            'livePriceSuffix' => $this->livePriceSuffix,
            'renderingWidth' => $this->renderingWidth ? $this->renderingWidth: 'input',
            'renderingHeight' => $this->renderingHeight ? $this->renderingHeight: 'input',
            'defaultWidth' => $this->defaultWidth,
            'defaultHeight' => $this->defaultHeight,
            'renderDialogInOnePageDesktop' => $this->renderDialogInOnePageDesktop
        ];
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return [
            'width' => (float) $this->defaultWidth,
            'height' => (float) $this->defaultHeight
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

        $humanReadableValues = [];

        foreach ($selectedValues as $property => $value) {
            switch ($property) {
                case 'width':
                    $humanReadableValues[$property] = AptoTranslatedValue::fromArray([
                        'de_DE' => $this->prefixWidth->getTranslation($de_DE, null, true)->getValue() . ' ' . $value . $this->suffixWidth->getTranslation($de_DE, null, true)->getValue(),
                        'en_GB' => $this->prefixWidth->getTranslation($en_GB, null, true)->getValue() . ' ' . $value . $this->suffixWidth->getTranslation($en_GB, null, true)->getValue()
                    ]);
                    break;
                case 'height':
                    $humanReadableValues[$property] = AptoTranslatedValue::fromArray([
                        'de_DE' => $this->prefixHeight->getTranslation($de_DE, null, true)->getValue() . ' ' . $value . $this->suffixHeight->getTranslation($de_DE, null, true)->getValue(),
                        'en_GB' => $this->prefixHeight->getTranslation($en_GB, null, true)->getValue() . ' ' . $value . $this->suffixHeight->getTranslation($en_GB, null, true)->getValue()
                    ]);
                    break;
            }
        }

        return $humanReadableValues;
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
                'width' => $this->elementWidthValues->jsonEncode(),
                'height' => $this->elementHeightValues->jsonEncode(),
                'priceMatrixId' => $this->priceMatrixId,
                'prefixWidth' => $this->prefixWidth->jsonSerialize(),
                'prefixHeight' => $this->prefixHeight->jsonSerialize(),
                'suffixWidth' => $this->suffixWidth->jsonSerialize(),
                'suffixHeight' => $this->suffixHeight->jsonSerialize(),
                'livePricePrefix' => $this->livePricePrefix->jsonSerialize(),
                'livePriceSuffix' => $this->livePriceSuffix->jsonSerialize(),
                'renderingWidth' => $this->renderingWidth,
                'renderingHeight' => $this->renderingHeight,
                'defaultWidth' => $this->defaultWidth,
                'defaultHeight' => $this->defaultHeight,
                'renderDialogInOnePageDesktop' => $this->renderDialogInOnePageDesktop
            ]
        ];
    }

    /**
     * @param array $json
     * @return ElementDefinition
     * @throws InvalidTranslatedValueException
     */
    public static function jsonDecode(array $json): ElementDefinition
    {
        if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'WidthHeightElementDefinition\' due to wrong class namespace.');
        }

        if (!isset($json['json']['priceMatrixId'])) {
            $json['json']['priceMatrixId'] = '';
        }

        if (
            !isset($json['json']['width']) ||
            !isset($json['json']['height'])
        ) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'WidthHeightElementDefinition\' due to missing values.');
        }

        if (!isset($json['json']['prefixWidth'])) {
            $json['json']['prefixWidth'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['prefixWidth'] = AptoTranslatedValue::fromArray($json['json']['prefixWidth']);
        }

        if (!isset($json['json']['prefixHeight'])) {
            $json['json']['prefixHeight'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['prefixHeight'] = AptoTranslatedValue::fromArray($json['json']['prefixHeight']);
        }

        if (!isset($json['json']['suffixWidth'])) {
            $json['json']['suffixWidth'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['suffixWidth'] = AptoTranslatedValue::fromArray($json['json']['suffixWidth']);
        }

        if (!isset($json['json']['suffixHeight'])) {
            $json['json']['suffixHeight'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['suffixHeight'] = AptoTranslatedValue::fromArray($json['json']['suffixHeight']);
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

        if (!isset($json['json']['renderingWidth'])) {
            $json['json']['renderingWidth'] = 'input';
        }

        if (!isset($json['json']['renderingHeight'])) {
            $json['json']['renderingHeight'] = 'input';
        }

        if (!isset($json['json']['defaultWidth'])) {
            $json['json']['defaultWidth'] = null;
        }

        if (!isset($json['json']['defaultHeight'])) {
            $json['json']['defaultHeight'] = null;
        }

        if (!isset($json['json']['renderDialogInOnePageDesktop'])) {
            $json['json']['renderDialogInOnePageDesktop'] = true;
        }

        return new self(
            ElementValueCollection::jsonDecode($json['json']['width']),
            ElementValueCollection::jsonDecode($json['json']['height']),
            $json['json']['priceMatrixId'],
            $json['json']['prefixWidth'],
            $json['json']['prefixHeight'],
            $json['json']['suffixWidth'],
            $json['json']['suffixHeight'],
            $json['json']['livePricePrefix'],
            $json['json']['livePriceSuffix'],
            $json['json']['renderingWidth'],
            $json['json']['renderingHeight'],
            $json['json']['defaultWidth'],
            $json['json']['defaultHeight'],
            $json['json']['renderDialogInOnePageDesktop']
        );
    }
}
