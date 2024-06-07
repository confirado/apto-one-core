<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementJsonValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementSingleTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;

class MaterialPickerElementDefinition implements ElementDefinition
{
    const NAME = 'Farb- bzw. Stoffauswahl Element';
    const BACKEND_COMPONENT = '<material-picker-element-definition definition-validation="setDefinitionValidation(definitionValidation)"></material-picker-element-definition>';
    const FRONTEND_COMPONENT = '<material-picker-element-definition section-ctrl="$ctrl.section" section="section" element="element"></material-picker-element-definition>';

    /**
     * @var string
     */
    protected $poolId;

    /**
     * @var string
     */
    protected $defaultMaterialId;

    /**
     * @var string
     */
    protected $defaultMaterialPoolId;

    /**
     * @var bool
     */
    protected $secondaryMaterialActive;

    /**
     * @var int
     */
    protected $secondaryMaterialAdditionalCharge;

    /**
     * @var string
     */
    protected $monochromeImage;

    /**
     * @var string
     */
    protected $multicoloredImageAlternately;

    /**
     * @var string
     */
    protected $multicoloredImageInput;

    /**
     * @var bool
     */
    protected $searchboxActive;

    /**
     * @var bool
     */
    protected $allowMultiple;

    /**
     * @var bool
     */
    protected $altColorSelect;

    /**
     * @var bool
     */
    protected $colorSectionActive;

    /**
     * @var bool
     */
    protected $priceGroupActive;

    /**
     * @var string
     */
    protected $sortByPosition;

    /**
     * @var bool
     */
    protected $showPriceGroupInMaterialName;

    /**
     * @var bool
     */
    protected $sortOrderActive;

    /**
     * @param string $poolId
     * @param string $defaultMaterialId
     * @param string $defaultMaterialPoolId
     * @param bool $secondaryMaterialActive
     * @param int $secondaryMaterialAdditionalCharge
     * @param string $monochromeImage
     * @param string $multicoloredImageAlternately
     * @param string $multicoloredImageInput
     * @param bool $searchboxActive
     * @param bool $allowMultiple
     * @param bool $altColorSelect
     * @param bool $colorSectionActive
     * @param bool $priceGroupActive
     * @param string $sortByPosition
     * @param bool $showPriceGroupInMaterialName
     * @param bool $sortOrderActive
     */
    public function __construct(
        string $poolId,
        string $defaultMaterialId,
        string $defaultMaterialPoolId,
        bool $secondaryMaterialActive,
        int $secondaryMaterialAdditionalCharge,
        string $monochromeImage,
        string $multicoloredImageAlternately,
        string $multicoloredImageInput,
        bool $searchboxActive,
        bool $allowMultiple,
        bool $altColorSelect,
        bool $colorSectionActive,
        bool $priceGroupActive,
        string $sortByPosition,
        bool $showPriceGroupInMaterialName,
        bool $sortOrderActive
    ) {
        $this->poolId = $poolId;
        $this->defaultMaterialId = $defaultMaterialId;
        $this->defaultMaterialPoolId = $defaultMaterialPoolId;
        $this->secondaryMaterialActive = $secondaryMaterialActive;
        $this->secondaryMaterialAdditionalCharge = $secondaryMaterialAdditionalCharge;
        $this->monochromeImage = $monochromeImage;
        $this->multicoloredImageAlternately = $multicoloredImageAlternately;
        $this->multicoloredImageInput = $multicoloredImageInput;
        $this->searchboxActive = $searchboxActive;
        $this->allowMultiple = $allowMultiple;
        $this->altColorSelect = $altColorSelect;
        $this->colorSectionActive = $colorSectionActive;
        $this->priceGroupActive = $priceGroupActive;
        $this->sortByPosition = $sortByPosition;
        $this->showPriceGroupInMaterialName = $showPriceGroupInMaterialName;
        $this->sortOrderActive = $sortOrderActive;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        return [
            'aptoElementDefinitionId' => new ElementValueCollection([new ElementSingleTextValue('apto-element-material-picker')]),
            'poolId' => new ElementValueCollection([new ElementTextValue(0, 36)]),
            'productId' => new ElementValueCollection([new ElementTextValue(0, 36)]),
            'materialId' => new ElementValueCollection([new ElementTextValue(0, 36)]),
            'materialName' => new ElementValueCollection([new ElementTextValue(0, 255)]),
            'priceGroup' => new ElementValueCollection([new ElementTextValue(0, 255)]),
            'materials' => new ElementValueCollection([new ElementJsonValue()]),
            'materialIdSecondary' => new ElementValueCollection([new ElementTextValue(0, 36)]),
            'materialNameSecondary' => new ElementValueCollection([new ElementTextValue(0, 255)]),
            'priceGroupSecondary' => new ElementValueCollection([new ElementTextValue(0, 255)]),
            'materialsSecondary' => new ElementValueCollection([new ElementJsonValue()]),
            'materialColorMixing' => new ElementValueCollection([new ElementTextValue(0, 255)]),
            'materialColorArrangement' => new ElementValueCollection([new ElementTextValue(0, 255)]),
            'materialColorQuantity' => new ElementValueCollection([new ElementTextValue(0, 255)])
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
            'aptoElementDefinitionId' => 'apto-element-material-picker',
            'poolId' => $this->poolId,
            'defaultMaterialId' => $this->defaultMaterialId,
            'secondaryMaterialActive' => $this->secondaryMaterialActive,
            'secondaryMaterialAdditionalCharge' => $this->secondaryMaterialAdditionalCharge,
            'monochromeImage' => $this->monochromeImage,
            'multicoloredImageAlternately' => $this->multicoloredImageAlternately,
            'multicoloredImageInput' => $this->multicoloredImageInput,
            'searchboxActive' => $this->searchboxActive,
            'allowMultiple' => $this->allowMultiple,
            'altColorSelect' => $this->altColorSelect,
            'colorSectionActive' => $this->colorSectionActive,
            'priceGroupActive' => $this->priceGroupActive,
            'sortByPosition' => $this->sortByPosition,
            'showPriceGroupInMaterialName' => $this->showPriceGroupInMaterialName,
            'sortOrderActive' => $this->sortOrderActive
        ];
    }

    /**
     * @param array $selectedValues
     * @return array
     * @todo find a solution to define the display order of human readable values
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        $materialName = '';
        $materialNameSecondary = '';

        if ($this->allowMultiple) {
            foreach ($selectedValues['materials'] as $key => $material) {
                if ($key !== 0) {
                    $materialName .= ', ';
                }
                $materialName .= $material['name'];
            }
            foreach ($selectedValues['materialsSecondary'] as $keySecondary => $materialSecondary) {
                if ($keySecondary !== 0) {
                    $materialNameSecondary .= ', ';
                }
                $materialNameSecondary .= $materialSecondary['name'];
            }
        } else {
            $materialName = $selectedValues['materialName'];
            $materialNameSecondary = $selectedValues['materialNameSecondary'];
        }

        $humanReadableValues = [
            'materialName' => AptoTranslatedValue::fromArray(
                [
                    'de_DE' => 'Stoff: ' . $materialName,
                    'en_GB' => 'Material: ' . $materialName
                ]
            )
        ];

        if (!$this->secondaryMaterialActive) {
            return $humanReadableValues;
        }

        if ('monochrome' === $selectedValues['materialColorMixing']) {
            $humanReadableValues['materialColorMixing'] = AptoTranslatedValue::fromArray(
                [
                    'de_DE' => 'Farbmischung: einfarbig',
                    'en_GB' => 'Color mixing: monochrome'
                ]
            );
        }

        if ('multicolored' === $selectedValues['materialColorMixing']) {
            $humanReadableValues['materialColorMixing'] = AptoTranslatedValue::fromArray(
                [
                    'de_DE' => 'Farbmischung: mehrfarbig',
                    'en_GB' => 'Color mixing: multicolored'
                ]
            );

            $humanReadableValues['materialNameSecondary'] = AptoTranslatedValue::fromArray(
                [
                    'de_DE' => 'Zweiter Stoff: ' . $materialNameSecondary,
                    'en_GB' => 'Secondary material: ' . $materialNameSecondary
                ]
            );
        }

        if ('alternately' === $selectedValues['materialColorArrangement']) {
            $humanReadableValues['materialColorArrangement'] = AptoTranslatedValue::fromArray(
                [
                    'de_DE' => 'Anordnung: jede zweite Paneele/Lamelle in zweiter Farbe',
                    'en_GB' => 'Arrangement: every second panel/lamella in secondary color '
                ]
            );
        }

        if ('input' === $selectedValues['materialColorArrangement']) {
            $humanReadableValues['materialColorArrangement'] = AptoTranslatedValue::fromArray(
                [
                    'de_DE' => 'Anordnung: freie Eingabe für zweite Paneel- / Lamellenfarbe',
                    'en_GB' => 'Arrangement: free input for secondary panel/lamella color'
                ]
            );

            $humanReadableValues['materialColorQuantity'] = AptoTranslatedValue::fromArray(
                [
                    'de_DE' => 'Anzahl: ' . $selectedValues['materialColorQuantity'],
                    'en_GB' => 'Quantity: ' . $selectedValues['materialColorQuantity']
                ]
            );
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
                'poolId' => $this->poolId,
                'defaultMaterialId' => $this->defaultMaterialId,
                'defaultMaterialPoolId' => $this->defaultMaterialPoolId,
                'secondaryMaterialActive' => $this->secondaryMaterialActive,
                'secondaryMaterialAdditionalCharge' => $this->secondaryMaterialAdditionalCharge,
                'monochromeImage' => $this->monochromeImage,
                'multicoloredImageAlternately' => $this->multicoloredImageAlternately,
                'multicoloredImageInput' => $this->multicoloredImageInput,
                'searchboxActive' => $this->searchboxActive,
                'allowMultiple' => $this->allowMultiple,
                'altColorSelect' => $this->altColorSelect,
                'colorSectionActive' => $this->colorSectionActive,
                'priceGroupActive' => $this->priceGroupActive,
                'sortByPosition' => $this->sortByPosition,
                'showPriceGroupInMaterialName' => $this->showPriceGroupInMaterialName,
                'sortOrderActive' => $this->sortOrderActive
            ]
        ];
    }

    /**
     * @param array $json
     * @return ElementDefinition
     */
    public static function jsonDecode(array $json): ElementDefinition
    {
        if (self::class !== $json['class']) {
            throw new \InvalidArgumentException(
                'Cannot convert json value to Type \'MaterialPickerElementDefinition\' due to wrong class namespace.'
            );
        }

        if (!isset($json['json']['poolId'])) {
            $json['json']['poolId'] = '';
        }

        if (!isset($json['json']['defaultMaterialId'])) {
            $json['json']['defaultMaterialId'] = '';
        }

        if (!isset($json['json']['defaultMaterialPoolId'])) {
            $json['json']['defaultMaterialPoolId'] = '';
        }

        if (!isset($json['json']['secondaryMaterialActive'])) {
            $json['json']['secondaryMaterialActive'] = false;
        }

        if (!array_key_exists('secondaryMaterialAdditionalCharge', $json['json'])) {
            $json['json']['secondaryMaterialAdditionalCharge'] = 1500;
        }

        if (!isset($json['json']['monochromeImage'])) {
            $json['json']['monochromeImage'] = '';
        }

        if (!isset($json['json']['multicoloredImageAlternately'])) {
            $json['json']['multicoloredImageAlternately'] = '';
        }

        if (!isset($json['json']['multicoloredImageInput'])) {
            $json['json']['multicoloredImageInput'] = '';
        }

        if (!isset($json['json']['searchboxActive'])) {
            $json['json']['searchboxActive'] = false;
        }

        if (!isset($json['json']['allowMultiple'])) {
            $json['json']['allowMultiple'] = false;
        }

        if (!isset($json['json']['altColorSelect'])) {
            $json['json']['altColorSelect'] = false;
        }

        if (!isset($json['json']['colorSectionActive'])) {
            $json['json']['colorSectionActive'] = true;
        }

        if (!isset($json['json']['priceGroupActive'])) {
            $json['json']['priceGroupActive'] = true;
        }

        /*  In the pas we had a checkbox for sortByPosition:
            true/checked was sort by position
            false/uncheck was the sort by clicks.

            now we have selectbox instead of checkbox with 3 possible values:
            'clicks', 'position', 'pricegroup'. */
        if (!isset($json['json']['sortByPosition'])) {
            $json['json']['sortByPosition'] = 'clicks';
        }
        else {
            if ($json['json']['sortByPosition'] === true) {
                $json['json']['sortByPosition'] = 'position';
            }
            else if ($json['json']['sortByPosition'] === false) {
                $json['json']['sortByPosition'] = 'clicks';
            }
        }

        if (!isset($json['json']['showPriceGroupInMaterialName'])) {
            $json['json']['showPriceGroupInMaterialName'] = false;
        }

        if (!isset($json['json']['sortOrderActive'])) {
            $json['json']['sortOrderActive'] = false;
        }

        return new self(
            $json['json']['poolId'],
            $json['json']['defaultMaterialId'],
            $json['json']['defaultMaterialPoolId'],
            $json['json']['secondaryMaterialActive'],
            $json['json']['secondaryMaterialAdditionalCharge'],
            $json['json']['monochromeImage'],
            $json['json']['multicoloredImageAlternately'],
            $json['json']['multicoloredImageInput'],
            $json['json']['searchboxActive'],
            $json['json']['allowMultiple'],
            $json['json']['altColorSelect'],
            $json['json']['colorSectionActive'],
            $json['json']['priceGroupActive'],
            $json['json']['sortByPosition'],
            $json['json']['showPriceGroupInMaterialName'],
            $json['json']['sortOrderActive']
        );
    }
}
