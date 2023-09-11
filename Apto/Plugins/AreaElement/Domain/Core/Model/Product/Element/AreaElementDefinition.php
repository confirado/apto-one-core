<?php

namespace Apto\Plugins\AreaElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionDefaultValues;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;

class AreaElementDefinition implements ElementDefinition, ElementDefinitionDefaultValues
{
    const NAME = 'Flächen Element';
    const BACKEND_COMPONENT = '<apto-area-element definition-validation="setDefinitionValidation(definitionValidation)"></apto-area-element>';
    const FRONTEND_COMPONENT = '<apto-area-element section-ctrl="$ctrl.section" section="section" element="element"></apto-area-element>';

    /**
     * @var boolean
     */
    protected $renderDialogInOnePageDesktop;

    /**
     * @var array
     */
    protected $priceMatrix;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var AptoTranslatedValue
     */
    protected $livePricePrefix;

    /**
     * @var AptoTranslatedValue
     */
    protected $livePriceSuffix;

    /**
     * @var ElementValueCollection|null
     */
    protected  $sumOfFieldValues;

    /**
     * @var array
     */
    protected $priceMultiplication;

    /**
     * AreaElementDefinition constructor.
     * @param bool $renderDialogInOnePageDesktop
     * @param array $priceMatrix
     * @param array $fields
     * @param AptoTranslatedValue $livePricePrefix
     * @param AptoTranslatedValue $livePriceSuffix
     * @param ElementValueCollection|null $sumOfFieldValues
     * @param array $priceMultiplication
     */
    public function __construct(
        bool $renderDialogInOnePageDesktop,
        array $priceMatrix,
        array $fields,
        AptoTranslatedValue $livePricePrefix,
        AptoTranslatedValue $livePriceSuffix,
        ?ElementValueCollection $sumOfFieldValues,
        array $priceMultiplication
    ) {
        $this->renderDialogInOnePageDesktop = $renderDialogInOnePageDesktop;
        $this->priceMatrix = $priceMatrix;
        $this->fields = $fields;
        $this->assertValidFields();
        $this->livePricePrefix = $livePricePrefix;
        $this->livePriceSuffix = $livePriceSuffix;
        $this->sumOfFieldValues = $sumOfFieldValues;
        $this->priceMultiplication = $priceMultiplication;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        $values = [];

        foreach ($this->fields as $key => $field) {
            $values['field_' . $key] = $field['values'];
        }

        if ($this->sumOfFieldValues !== null) {
            $values['sumOfFieldValue'] = $this->sumOfFieldValues;
        }

        return $values;
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getComputableValues(array $selectedValues): array
    {
        $perimeter = 0;
        foreach ($selectedValues as $value) {
            $perimeter += $value;
        }
        return [
            'perimeter' => $perimeter
        ];
    }

    /**
     * @return array
     */
    public function getStaticValues(): array
    {
        $fields = [];

        foreach ($this->fields as $field) {
            $fields[] = [
                'prefix' => $field['prefix'],
                'suffix' => $field['suffix'],
                'rendering' => $field['rendering'],
                'default' => $field['default']
            ];
        }

        return [
            'aptoElementDefinitionId' => 'apto-element-area-element',
            'renderDialogInOnePageDesktop' => $this->renderDialogInOnePageDesktop,
            'priceMatrix' => $this->priceMatrix,
            'fields' => $fields,
            'livePricePrefix' => $this->livePricePrefix,
            'livePriceSuffix' => $this->livePriceSuffix,
            'sumOfFieldValueActive' => $this->sumOfFieldValues === null ? false : true,
            'priceMultiplication' => $this->priceMultiplication
        ];
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        $defaultValues= [];

        foreach ($this->fields as $index => $field) {
            $defaultValues['field_' . $index] = (float) $field['default'];
        }

        return $defaultValues;
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        $humanReadableValues = [];

        // set locales
        $de_DE = new AptoLocale('de_DE');
        $en_EN = new AptoLocale('en_EN');

        foreach ($selectedValues as $property => $value) {
            if($property === 'sumOfFieldValue') {
                continue;
            }
            // get field index from generated selected value key
            // see $this->getSelectableValues
            $fieldIndex = explode('_', $property)[1];

            /** @var AptoTranslatedValue $prefix */
            $prefix = $this->fields[$fieldIndex]['prefix'];

            /** @var AptoTranslatedValue $suffix */
            $suffix = $this->fields[$fieldIndex]['suffix'];

            // set human readable value
            $humanReadableValues[$property] = AptoTranslatedValue::fromArray([
                'de_DE' => $prefix->getTranslation($de_DE, null, true)->getValue() . ' ' . $value . $suffix->getTranslation($de_DE, null, true)->getValue(),
                'en_EN' => $prefix->getTranslation($en_EN, null, true)->getValue() . ' ' . $value . $suffix->getTranslation($en_EN, null, true)->getValue()
            ]);
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
        $fields = [];
        foreach ($this->fields as $field) {
            /** @var AptoTranslatedValue $prefix */
            $prefix = $field['prefix'];

            /** @var AptoTranslatedValue $suffix */
            $suffix = $field['suffix'];

            /** @var ElementValueCollection $values */
            $values = $field['values'];

            $fields[] = [
                'prefix' => $prefix->jsonSerialize(),
                'suffix' => $suffix->jsonSerialize(),
                'rendering' => $field['rendering'],
                'default' => $field['default'],
                'values' => $values->jsonEncode()
            ];
        }

        $sumOfFieldValues = null;
        if ($this->sumOfFieldValues !== null) {
            $sumOfFieldValues = $this->sumOfFieldValues->jsonEncode();
        }

        return [
            'class' => get_class($this),
            'json' => [
                'renderDialogInOnePageDesktop' => $this->renderDialogInOnePageDesktop,
                'priceMatrix' => $this->priceMatrix,
                'fields' => $fields,
                'livePricePrefix' => $this->livePricePrefix->jsonSerialize(),
                'livePriceSuffix' => $this->livePriceSuffix->jsonSerialize(),
                'sumOfFieldValues' => $sumOfFieldValues,
                'priceMultiplication' => $this->priceMultiplication
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'AreaElementDefinition\' due to wrong class namespace.');
        }

        if (!isset($json['json']['renderDialogInOnePageDesktop'])) {
            $json['json']['renderDialogInOnePageDesktop'] = true;
        }

        if (!isset($json['json']['priceMatrix'])) {
            $json['json']['priceMatrix'] = [
                'id' => null,
                'row' => null,
                'column' => null
            ];
        }

        // set fields
        if (!isset($json['json']['fields'])) {
            $json['json']['fields'] = [[
                'prefix' => [],
                'suffix' => [],
                'rendering' => 'input',
                'default' => null,
                'values' => []
            ]];
        }

        foreach ($json['json']['fields'] as &$field) {
            // set prefix
            if (!isset($field['prefix'])) {
                $field['prefix'] = new AptoTranslatedValue([]);
            } else {
                $field['prefix'] = AptoTranslatedValue::fromArray($field['prefix']);
            }

            // set suffix
            if (!isset($field['suffix'])) {
                $field['suffix'] = new AptoTranslatedValue([]);
            } else {
                $field['suffix'] = AptoTranslatedValue::fromArray($field['suffix']);
            }

            // set rendering
            if (!isset($field['rendering'])) {
                $field['rendering'] = 'input';
            }

            // set default
            if (!isset($field['default'])) {
                $field['default'] = null;
            }

            // set values
            if (!isset($field['values'])) {
                throw new \InvalidArgumentException('Cannot convert json value to Type \'AreaElementDefinition\' due to missing values.');
            }
            $field['values'] = ElementValueCollection::jsonDecode($field['values']);
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

        $sumOfFieldValues = null;
        if (isset($json['json']['sumOfFieldValues'])) {
            $sumOfFieldValues = ElementValueCollection::jsonDecode($json['json']['sumOfFieldValues']);
        }

        $priceMultiplication = [];
        if (isset($json['json']['priceMultiplication'])) {
            $priceMultiplication = $json['json']['priceMultiplication'];
        }

        return new self(
            $json['json']['renderDialogInOnePageDesktop'],
            $json['json']['priceMatrix'],
            $json['json']['fields'],
            $json['json']['livePricePrefix'],
            $json['json']['livePriceSuffix'],
            $sumOfFieldValues,
            $priceMultiplication
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertValidFields()
    {
        foreach ($this->fields as $key => $field) {
            // check valid prefix
            if(!($field['prefix'] instanceof AptoTranslatedValue)) {
                throw new \InvalidArgumentException('Field ' . ($key + 1) . ' prefix must be an instance of AptoTranslatedValue');
            }

            // check valid suffix
            if(!($field['suffix'] instanceof AptoTranslatedValue)) {
                throw new \InvalidArgumentException('Field ' . ($key + 1) . ' suffix must be an instance of AptoTranslatedValue');
            }

            // check valid values
            if(!($field['values'] instanceof ElementValueCollection)) {
                throw new \InvalidArgumentException('Field ' . ($key + 1) . ' values must be an instance of ElementValueCollection');
            }

            // check valid default value
            if(
                null !== $field['default'] &&
                !$field['values']->contains($field['default'])
            ) {
                throw new \InvalidArgumentException('Field ' . ($key + 1) . ' default value \'' . $field['default'] . '\' is not a part of field ' . ($key + 1) . ' values.');
            }
        }
    }
}
