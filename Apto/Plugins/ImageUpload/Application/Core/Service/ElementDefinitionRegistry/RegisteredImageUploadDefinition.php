<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Product\Element\ImageUploadDefinition;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Service\Converter\MimeTypeExtensionConverter;

class RegisteredImageUploadDefinition implements RegisteredElementDefinition
{
    /**
     * @return string
     */
    public function getElementDefinitionName(): string
    {
        return ImageUploadDefinition::NAME;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClassName(): string
    {
        return ImageUploadDefinition::class;
    }

    /**
     * @return string
     */
    public function getBackendComponent(): string
    {
        return ImageUploadDefinition::BACKEND_COMPONENT;
    }

    /**
     * @return string
     */
    public function getFrontendComponent(): string
    {
        return ImageUploadDefinition::FRONTEND_COMPONENT;
    }

    /**
     * @param array $definitionValues
     * @return ElementDefinition
     */
    public function getElementDefinition(array $definitionValues): ElementDefinition
    {
        if (!isset($definitionValues['canvas'])) {
            $definitionValues['canvas'] = [
                'source' => 'Element',
                'canvasId' => null
            ];
        }

        if ($definitionValues['canvas']['source'] === 'Global' && null === $definitionValues['canvas']['canvasId']) {
            $definitionValues['canvas']['source'] = 'Element';
        }

        if (!isset($definitionValues['user'])) {
            $definitionValues['user'] = [
                'previewSize' => '250',
                'maxFileSize' => '4',
                'minWidth' => 0,
                'minHeight' => 0,
                'allowedFileTypes' => ['jpg', 'jpeg', 'png']
            ];
        }

        if (!isset($definitionValues['user']['useSurchargeAsReplacement'])) {
            $definitionValues['user']['useSurchargeAsReplacement'] = false;
        }

        if (!isset($definitionValues['user']['surchargePrices'])) {
            $definitionValues['user']['surchargePrices'] = [];
        }

        if (!isset($definitionValues['background'])) {
            $definitionValues['background'] = [
                'image' => null,
                'width' => '1000',
                'height' => '600',
                'perspective' => 'persp1',
                'layer' => '0',
                'offset' => [
                    'x' => '0',
                    'y' => '0'
                ]
            ];
        }

        if (!isset($definitionValues['text'])) {
            $definitionValues['text'] = [
                'active' => false,
                'options' => []
            ];
        }

        if (!isset($definitionValues['text']['default'])) {
            $definitionValues['text']['default'] = 'Mein Text!';
        }

        if (!isset($definitionValues['text']['fontSize'])) {
            $definitionValues['text']['fontSize'] = 25;
        }

        if (!isset($definitionValues['text']['fontFamily'])) {
            $definitionValues['text']['fontFamily'] = 'Open Sans';
        }

        if (!isset($definitionValues['text']['textAlign'])) {
            $definitionValues['text']['textAlign'] = 'center';
        }

        if (!isset($definitionValues['text']['fill'])) {
            $definitionValues['text']['fill'] = '#ffffff';
        }

        if (!isset($definitionValues['text']['multiline'])) {
            $definitionValues['text']['multiline'] = false;
        }

        if (!isset($definitionValues['text']['fonts'])) {
            $definitionValues['text']['fonts'] = [];
        }

        // add mime types
        $mimeTypeExtensionConverter = new MimeTypeExtensionConverter();
        $definitionValues['user']['allowedMimeTypes'] = $mimeTypeExtensionConverter->extensionsToMimeTypes(
            $definitionValues['user']['allowedFileTypes']
        );

        return new ImageUploadDefinition(
            $definitionValues['canvas'],
            $definitionValues['user'],
            $definitionValues['background'],
            $definitionValues['text']
        );
    }
}
