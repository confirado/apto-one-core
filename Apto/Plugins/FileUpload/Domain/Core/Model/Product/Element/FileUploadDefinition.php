<?php

namespace Apto\Plugins\FileUpload\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementJsonValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\FileUpload\Domain\Core\Model\Service\Converter\MimeTypeExtensionConverter;

class FileUploadDefinition implements ElementDefinition
{
    const NAME = 'File Upload Element';
    //const BACKEND_COMPONENT = '<apto-file-upload-element definition-validation="setDefinitionValidation(definitionValidation)" product-id="productId" section-id="sectionId" element="detail"></apto-file-upload-element>';
    const BACKEND_COMPONENT = 'hide';
    const FRONTEND_COMPONENT = '<apto-file-upload-element section-ctrl="$ctrl.section" section="section" element="element"></apto-file-upload-element>';

    /**
     * @var array
     */
    protected $file;

    /**
     * @var bool
     */
    protected $needsValue;

    /**
     * @var ElementValueCollection|null
     */
    protected $elementValues;

    /**
     * @var AptoTranslatedValue
     */
    protected $valuePrefix;

    /**
     * @var AptoTranslatedValue
     */
    protected $valueSuffix;

    /**
     * @param array $file
     * @param bool $needsValue
     * @param ElementValueCollection|null $elementValues
     * @param AptoTranslatedValue $valuePrefix
     * @param AptoTranslatedValue $valueSuffix
     */
    public function __construct(
        array $file,
        bool $needsValue,
        ?ElementValueCollection $elementValues,
        AptoTranslatedValue $valuePrefix,
        AptoTranslatedValue $valueSuffix
    ) {
        $this->file = $file;
        $this->needsValue = $needsValue;
        $this->elementValues = $elementValues;
        $this->valuePrefix = $valuePrefix;
        $this->valueSuffix = $valueSuffix;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        $values = [
            'aptoElementDefinitionId' => new ElementValueCollection([new ElementJsonValue()]),
            'file' => new ElementValueCollection([new ElementJsonValue()])
        ];

        if ($this->needsValue) {
            $values['value'] = $this->elementValues;
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
     * @return array
     */
    public function getStaticValues(): array
    {
        return [
            'aptoElementDefinitionId' => 'apto-element-file-upload',
			'file' => $this->file,
			'needsValue' => $this->needsValue,
			'valuePrefix' => $this->valuePrefix,
			'valueSuffix' => $this->valueSuffix
		];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        $values = [
            'file' => AptoTranslatedValue::fromArray([
                'de_DE' => 'Datei: ' . $selectedValues['file']['fileName'],
                'en_GB' => 'File: ' .$selectedValues['file']['fileName'],
            ])
        ];

		$de_DE = new AptoLocale('de_DE');
		$en_GB = new AptoLocale('en_GB');

		if ($this->needsValue && array_key_exists('value', $selectedValues)){
			$values['value'] = AptoTranslatedValue::fromArray([
				'de_DE' => $this->valuePrefix->getTranslation($de_DE, null, true)->getValue() . ' ' . $selectedValues['value'] . $this->valueSuffix->getTranslation($de_DE, null, true)->getValue(),
				'en_GB' => $this->valuePrefix->getTranslation($en_GB, null, true)->getValue() . ' ' . $selectedValues['value'] . $this->valueSuffix->getTranslation($en_GB, null, true)->getValue(),
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
                'file' => $this->file,
                'needsValue' => $this->needsValue,
                'value' => $this->elementValues->jsonEncode(),
                'valuePrefix' => $this->valuePrefix->jsonSerialize(),
                'valueSuffix' => $this->valueSuffix->jsonSerialize(),
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'FileUploadDefinition\' due to wrong class namespace.');
        }

        if (!isset($json['json']['file'])) {
            $json['json']['file'] = [
                'maxFileSize' => '4',
                'allowedFileTypes' => ['jpg']
            ];
        }

        // add mime types
        $mimeTypeExtensionConverter = new MimeTypeExtensionConverter();
        $json['json']['file']['allowedMimeTypes'] = $mimeTypeExtensionConverter->extensionsToMimeTypes(
            $json['json']['file']['allowedFileTypes']
        );

        // set value properties
        if (!isset($json['json']['needsValue'])) {
            $json['json']['needsValue'] = false;
        }

        if (!isset($json['json']['value'])) {
            $json['json']['value'] = null;
        } else {
            $json['json']['value'] = ElementValueCollection::jsonDecode($json['json']['value']);
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

        if ($json['json']['needsValue'] && !$json['json']['value']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'FileUploadDefinition\' due to missing values.');
        }

        return new self(
            $json['json']['file'],
            (bool) $json['json']['needsValue'],
            $json['json']['value'],
            $json['json']['valuePrefix'],
            $json['json']['valueSuffix']
        );
    }
}
