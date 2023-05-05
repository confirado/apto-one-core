<?php

namespace Apto\Plugins\PricePerUnitElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementBoolValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionCopyAware;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Doctrine\Common\Collections\Collection;

use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;

class PricePerUnitElementDefinition implements ElementDefinition, ElementDefinitionCopyAware
{
    const NAME = 'PricePerUnit Element';
    const BACKEND_COMPONENT = '<price-per-unit-element-definition definition-validation="setDefinitionValidation(definitionValidation)" product-id="productId" section-id="sectionId" element="detail"></price-per-unit-element-definition>';
    const FRONTEND_COMPONENT = '<price-per-unit-element-definition section-ctrl="$ctrl.section" section="section" element="element"></price-per-unit-element-definition>';

    /**
     * @var string|null
     */
    protected $sectionId;

    /**
     * @var string|null
     */
    protected $elementId;

    /**
     * @var string|null
     */
    protected $selectableValue;

    /**
     * @var string|null
     */
    protected $selectableValueType;

    /**
     * @var string
     */
    protected $conversionFactor;

    /**
     * @var bool
     */
    protected $minOne;

    /**
     * @var bool
     */
    protected $textBoxEnabled;

    /**
     * @var AptoTranslatedValue
     */
    protected $textBoxPrefix;

    /**
     * @var AptoTranslatedValue
     */
    protected $textBoxSuffix;

    /**
     * @var AptoTranslatedValue
     */
    protected $livePricePrefix;

    /**
     * @var AptoTranslatedValue
     */
    protected $livePriceSuffix;

    /**
     * @var ElementValueCollection
     */
    protected $elementTextValues;

    /**
     * @var array
     */
    protected $elementValueRefs;

    /**
     * PricePerUnitElementDefinition constructor.
     * @param string $conversionFactor
     * @param bool $minOne
     * @param bool $textBoxEnabled
     * @param AptoTranslatedValue $textBoxPrefix
     * @param AptoTranslatedValue $textBoxSuffix
     * @param AptoTranslatedValue $livePricePrefix
     * @param AptoTranslatedValue $livePriceSuffix
     * @param ElementValueCollection $elementTextValues
     * @param array $elementValueRefs
     * @param string|null $sectionId
     * @param string|null $elementId
     * @param string|null $selectableValue
     * @param string|null $selectableValueType
     */
    public function __construct(
        string $conversionFactor,
        bool $minOne,
        bool $textBoxEnabled,
        AptoTranslatedValue $textBoxPrefix,
        AptoTranslatedValue $textBoxSuffix,
        AptoTranslatedValue $livePricePrefix,
        AptoTranslatedValue $livePriceSuffix,
        ElementValueCollection $elementTextValues,
        array $elementValueRefs,
        string $sectionId = null,
        string $elementId = null,
        string $selectableValue = null,
        string $selectableValueType = null
    )
    {
        $this->conversionFactor = $conversionFactor;
        $this->minOne = $minOne;
        $this->textBoxEnabled = $textBoxEnabled;
        $this->textBoxPrefix = $textBoxPrefix;
        $this->textBoxSuffix = $textBoxSuffix;
        $this->livePricePrefix = $livePricePrefix;
        $this->livePriceSuffix = $livePriceSuffix;
        $this->elementTextValues = $elementTextValues;
        $this->elementValueRefs = $elementValueRefs;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->selectableValue = $selectableValue;
        $this->selectableValueType = $selectableValueType;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        $values = [
            'active' => new ElementValueCollection([
                new ElementBoolValue()
            ]),
            'text' => $this->elementTextValues
        ];
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
     * @return array
     */
    public function getStaticValues(): array
    {
        return [
            'aptoElementDefinitionId' => 'apto-element-price-per-unit',
            'sectionId' => $this->sectionId,
            'elementId' => $this->elementId,
            'selectableValue' => $this->selectableValue,
            'selectableValueType' => $this->selectableValueType,
            'conversionFactor' => $this->conversionFactor,
            'minOne' => $this->minOne,
            'textBoxEnabled' => $this->textBoxEnabled,
            'textBoxPrefix' => $this->textBoxPrefix,
            'textBoxSuffix' => $this->textBoxSuffix,
            'livePricePrefix' => $this->livePricePrefix,
            'livePriceSuffix' => $this->livePriceSuffix,
            'elementValueRefs' => $this->elementValueRefs
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

        $humanReadableValues = [];

        foreach ($selectedValues as $property => $value) {
            switch ($property) {
                case 'text':
                    $humanReadableValues[$property] = AptoTranslatedValue::fromArray([
                        'de_DE' => $this->textBoxPrefix->getTranslation($de_DE, null, true)->getValue() . ' ' . $value . $this->textBoxSuffix->getTranslation($de_DE, null, true)->getValue(),
                        'en_EN' => $this->textBoxPrefix->getTranslation($en_EN, null, true)->getValue() . ' ' . $value . $this->textBoxSuffix->getTranslation($en_EN, null, true)->getValue()
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
                'sectionId' => $this->sectionId,
                'elementId' => $this->elementId,
                'selectableValue' => $this->selectableValue,
                'selectableValueType' => $this->selectableValueType,
                'conversionFactor' => $this->conversionFactor,
                'minOne' => $this->minOne,
                'textBoxEnabled' => $this->textBoxEnabled,
                'textBoxPrefix' => $this->textBoxPrefix->jsonSerialize(),
                'textBoxSuffix' => $this->textBoxSuffix->jsonSerialize(),
                'livePricePrefix' => $this->livePricePrefix->jsonSerialize(),
                'livePriceSuffix' => $this->livePriceSuffix->jsonSerialize(),
                'text' => $this->elementTextValues->jsonEncode(),
                'elementValueRefs' => $this->elementValueRefs
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'PricePerUnitElementDefinition\' due to wrong class namespace.');
        }

        if (!isset($json['json']['conversionFactor'])) {
            $json['json']['conversionFactor'] = '1.0';
        }

        if (!isset($json['json']['minOne'])) {
            $json['json']['minOne'] = false;
        }

        if (!isset($json['json']['textBoxEnabled'])) {
            $json['json']['textBoxEnabled'] = false;
        }

        if (!isset($json['json']['textBoxPrefix'])) {
            $json['json']['textBoxPrefix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['textBoxPrefix'] = AptoTranslatedValue::fromArray($json['json']['textBoxPrefix']);
        }

        if (!isset($json['json']['textBoxSuffix'])) {
            $json['json']['textBoxSuffix'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['textBoxSuffix'] = AptoTranslatedValue::fromArray($json['json']['textBoxSuffix']);
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

        if($json['json']['selectableValue'] && !array_key_exists('selectableValueType', $json['json'])) {
            $json['json']['selectableValueType'] = 'Selectable';
        }

        if(!isset($json['json']['selectableValueType'])) {
            $json['json']['selectableValueType'] = null;
        }

        if (!isset($json['json']['elementValueRefs'])) {
            $json['json']['elementValueRefs'] = [];
        }

        if (
            ($json['json']['sectionId'] !== null && !isset($json['json']['sectionId'])) ||
            ($json['json']['elementId'] !== null && !isset($json['json']['elementId'])) ||
            ($json['json']['selectableValue'] !== null && !isset($json['json']['selectableValue'])) ||
            ($json['json']['selectableValueType'] !== null && !isset($json['json']['selectableValueType'])) ||
            !isset($json['json']['text'])
        ) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'PricePerUnitElementDefinition\' due to missing values.');
        }

        if (false === $json['json']['textBoxEnabled']) {
            $elementTextValues = new ElementValueCollection([
                new ElementTextValue()
            ]);
        } else {
            $elementTextValues = ElementValueCollection::jsonDecode($json['json']['text']);
        }

        return new self(
            $json['json']['conversionFactor'],
            $json['json']['minOne'],
            $json['json']['textBoxEnabled'],
            $json['json']['textBoxPrefix'],
            $json['json']['textBoxSuffix'],
            $json['json']['livePricePrefix'],
            $json['json']['livePriceSuffix'],
            $elementTextValues,
            $json['json']['elementValueRefs'],
            $json['json']['sectionId'],
            $json['json']['elementId'],
            $json['json']['selectableValue'],
            $json['json']['selectableValueType']
        );
    }

    /**
     * @param Collection $entityMapping
     * @return ElementDefinition
     * @throws InvalidTranslatedValueException
     */
    public function copy(Collection &$entityMapping): ElementDefinition
    {
        $json = json_encode($this->jsonEncode());

        // replace all old entity ids with new ones
        /** @var AptoEntity $entity */
        foreach ($entityMapping->getKeys() as $key) {
            $entity = $entityMapping->get($key);
            $json = str_replace($key, $entity->getId()->getId(), $json);
        }

        return self::jsonDecode(json_decode($json, true));
    }
}