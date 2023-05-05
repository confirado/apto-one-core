<?php

namespace Apto\Plugins\FileUpload\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementRangeValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\FileUpload\Domain\Core\Model\Product\Element\FileUploadDefinition;
use Apto\Plugins\FileUpload\Domain\Core\Model\Service\Converter\MimeTypeExtensionConverter;

class RegisteredFileUploadDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return FileUploadDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return FileUploadDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return FileUploadDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return FileUploadDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     * @throws InvalidTranslatedValueException
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        if (!isset($definitionValues['file'])) {
            $definitionValues['file'] = [
                'maxFileSize' => '4',
                'allowedFileTypes' => ['jpg']
            ];
        }

        // add mime types
        $mimeTypeExtensionConverter = new MimeTypeExtensionConverter();
        $definitionValues['file']['allowedMimeTypes'] = $mimeTypeExtensionConverter->extensionsToMimeTypes(
            $definitionValues['file']['allowedFileTypes']
        );

        // set value properties
        if (!isset($definitionValues['needsValue'])) {
            $definitionValues['needsValue'] = false;
        }

        if (!isset($definitionValues['value'])) {
            $definitionValues['value'] = null;
        } else {
            $values = [];
            foreach ($definitionValues['value'] as $value) {
                $values[] = new ElementRangeValue($value['minimum'], $value['maximum'], $value['step']);
            }
            $definitionValues['value'] = new ElementValueCollection($values);
        }

        if (!isset($definitionValues['valuePrefix'])) {
            $definitionValues['valuePrefix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['valuePrefix'] = AptoTranslatedValue::fromArray($definitionValues['valuePrefix']);
        }

        if (!isset($definitionValues['valueSuffix'])) {
            $definitionValues['valueSuffix'] = new AptoTranslatedValue([]);
        } else {
            $definitionValues['valueSuffix'] = AptoTranslatedValue::fromArray($definitionValues['valueSuffix']);
        }

        if ($definitionValues['needsValue'] && !$definitionValues['value']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'FileUploadDefinition\' due to missing values.');
        }

        return new FileUploadDefinition(
            $definitionValues['file'],
            (bool) $definitionValues['needsValue'],
            $definitionValues['value'],
            $definitionValues['valuePrefix'],
            $definitionValues['valueSuffix']
        );
    }
}
