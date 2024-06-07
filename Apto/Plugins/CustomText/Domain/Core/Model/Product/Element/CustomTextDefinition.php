<?php

namespace Apto\Plugins\CustomText\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementSingleTextValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Catalog\Domain\Core\Model\Product\Element\InvalidSelectablePropertyException;

class CustomTextDefinition implements ElementDefinition
{
    const NAME = 'Custom Text Element';
    const BACKEND_COMPONENT = '<custom-text definition-validation="setDefinitionValidation(definitionValidation)"></custom-text>';
    const FRONTEND_COMPONENT = '<custom-text section-ctrl="$ctrl.section" section="section" element="element"></custom-text>';

    /**
     * @var ElementValueCollection
     */
    protected $textValues;

    /**
     * @var string
     */
    protected $rendering;

    /**
     * @var AptoTranslatedValue
     */
    protected $placeholder;

    /**
     * @var boolean
     */
    protected $renderDialogInOnePageDesktop;

    /**
     * @param ElementValueCollection $textValues
     * @param string $rendering
     * @param AptoTranslatedValue $placeholder
     * @param bool $renderDialogInOnePageDesktop
     */
    public function __construct(
        ElementValueCollection $textValues,
        string $rendering,
        AptoTranslatedValue $placeholder,
        bool $renderDialogInOnePageDesktop = true
    ) {
        $this->textValues = $textValues;
        $this->rendering = $rendering;
        $this->placeholder = $placeholder;
        $this->renderDialogInOnePageDesktop = $renderDialogInOnePageDesktop;
    }

    /**
     * @return array
     * @throws InvalidSelectablePropertyException
     */
    public function getSelectableValues(): array
    {
        return [
            'aptoElementDefinitionId' => new ElementValueCollection([new ElementSingleTextValue('apto-element-custom-text')]),
            'text' => $this->textValues
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
            'aptoElementDefinitionId' => 'apto-element-custom-text',
            'rendering' => $this->rendering,
            'placeholder' => $this->placeholder,
            'renderDialogInOnePageDesktop' => $this->renderDialogInOnePageDesktop
        ];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        return [
            'text' => AptoTranslatedValue::fromArray([
                'de_DE' => $selectedValues['text'],
                'en_GB' => $selectedValues['text']
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
                'text' => $this->textValues->jsonEncode(),
                'rendering' => $this->rendering,
                'placeholder' => $this->placeholder->jsonSerialize(),
                'renderDialogInOnePageDesktop' => $this->renderDialogInOnePageDesktop
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'CustomTextDefinition\' due to wrong class namespace.');
        }

        if (!isset($json['json']['text'])) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'CustomTextDefinition\' due to missing values.');
        }

        if (!isset($json['json']['rendering'])) {
            $json['json']['rendering'] = 'input';
        }

        if (!isset($json['json']['placeholder'])) {
            $json['json']['placeholder'] = new AptoTranslatedValue([]);
        } else {
            $json['json']['placeholder'] = AptoTranslatedValue::fromArray($json['json']['placeholder']);
        }

        if (!isset($json['json']['renderDialogInOnePageDesktop'])) {
            $json['json']['renderDialogInOnePageDesktop'] = true;
        }

        return new self(
            ElementValueCollection::jsonDecode(
                $json['json']['text']
            ),
            $json['json']['rendering'],
            $json['json']['placeholder'],
            $json['json']['renderDialogInOnePageDesktop']
        );
    }
}
