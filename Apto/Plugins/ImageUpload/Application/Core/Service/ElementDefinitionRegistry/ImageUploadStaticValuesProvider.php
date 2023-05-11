<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\ElementStaticValuesProvider;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\ImageUpload\Application\Core\Query\CanvasFinder;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Product\Element\ImageUploadDefinition;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Service\Converter\MimeTypeExtensionConverter;

class ImageUploadStaticValuesProvider implements ElementStaticValuesProvider
{
    /**
     * @var CanvasFinder
     */
    private $canvasFinder;

    /**
     * @param CanvasFinder $canvasFinder
     */
    public function __construct(CanvasFinder $canvasFinder)
    {
        $this->canvasFinder = $canvasFinder;
    }

    /**
     * @return string
     */
    public function getElementDefinitionClass(): string
    {
        return ImageUploadDefinition::class;
    }

    /**
     * @param ElementDefinition $elementDefinition
     * @return array
     * @throws InvalidUuidException
     */
    public function getStaticValues(ElementDefinition $elementDefinition): array
    {
        $staticValues = $elementDefinition->getStaticValues();
        $source = $staticValues['canvas']['source'];
        $canvasId = $staticValues['canvas']['canvasId'];

        if ($source === 'Element' || null === $canvasId) {
            return $staticValues;
        }

        $canvas = $this->canvasFinder->findById(new AptoUuid($canvasId));
        if (null === $canvas) {
            return $staticValues;
        }

        // set static values from global canvas settings
        $staticValues['user'] = array_merge($canvas['imageSettings'], $canvas['priceSettings']);
        $staticValues['text'] = $canvas['textSettings'];
        $staticValues['background'] = $canvas['areaSettings'];

        // add mime types
        $mimeTypeExtensionConverter = new MimeTypeExtensionConverter();
        $staticValues['user']['allowedMimeTypes'] = $mimeTypeExtensionConverter->extensionsToMimeTypes(
            $staticValues['user']['allowedFileTypes']
        );

        return $staticValues;
    }
}
