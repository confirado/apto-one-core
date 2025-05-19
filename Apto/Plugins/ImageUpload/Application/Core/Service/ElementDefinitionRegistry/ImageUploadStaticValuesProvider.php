<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Service\ElementDefinitionRegistry;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\ElementStaticValuesProvider;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Plugins\ImageUpload\Application\Core\Query\Canvas\CanvasFinder;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Product\Element\ImageUploadDefinition;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Service\Converter\MimeTypeExtensionConverter;

class ImageUploadStaticValuesProvider implements ElementStaticValuesProvider
{
    /**
     * @var CanvasFinder
     */
    private $canvasFinder;

    /**
     * @var MediaFileSystemConnector
     */
    private $mediaFileSystemConnector;

    /**
     * @param CanvasFinder $canvasFinder
     * @param MediaFileSystemConnector $mediaFileSystemConnector
     */
    public function __construct(CanvasFinder $canvasFinder, MediaFileSystemConnector $mediaFileSystemConnector)
    {
        $this->canvasFinder = $canvasFinder;
        $this->mediaFileSystemConnector = $mediaFileSystemConnector;
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
        $staticValues = $this->convertElementStaticValuesToCanvasStaticValues($elementDefinition->getStaticValues());
        $source = $staticValues['canvas']['source'];
        $canvasId = $staticValues['canvas']['canvasId'];

        // if it's a legacy configuration it will be returned here, otherwise it will be
        // treated and changed to new config afterwards and then returned
        if ($source === 'Element' || null === $canvasId) {
            return $staticValues;
        }

        $canvas = $this->canvasFinder->findById(new AptoUuid($canvasId));
        if (null === $canvas) {
            return $staticValues;
        }

        // set static values from global canvas settings
        $staticValues['image'] = $canvas['imageSettings'];
        $motives = [];

        foreach ($canvas['motiveSettings'] as $singleMotive) {
            $singleMotive['files'] = $this->getMotiveFiles($singleMotive);
            $motives[$singleMotive['perspective'] ?? 'persp1'] = $singleMotive;
        }

        $staticValues['motive'] = $motives;
        $textData = $canvas['textSettings'];
        $textData['boxes'] = [];

        if (isset($canvas['textSettings']['boxes'])) {
            foreach ($canvas['textSettings']['boxes'] as $box) {
                $box['perspective'] = $box['perspective'] ?? 'persp1';
                $textData['boxes'][] = $box;
            }
        }

        $staticValues['text'] = $textData;
        $staticValues['area'] = $canvas['areaSettings'];
        $staticValues['price'] = $canvas['priceSettings'];
        $imagesConverted = [];
        // add mime types
        $mimeTypeExtensionConverter = new MimeTypeExtensionConverter();
        foreach ($staticValues['image'] as $singleImageSettings) {
            $singleImageSettings['allowedMimeTypes'] = $mimeTypeExtensionConverter->extensionsToMimeTypes(
                $singleImageSettings['allowedFileTypes']
            );
            $imagesConverted[$singleImageSettings['perspective'] ?? 'persp1'] = $singleImageSettings;
        }
        $staticValues['image'] = $imagesConverted;

        return $staticValues;
    }

    /**
     * @param array $staticValues
     * @return array
     */
    private function convertElementStaticValuesToCanvasStaticValues(array $staticValues)
    {
        if (array_key_exists('background', $staticValues)) {
            $area = $staticValues['background']['area'];
            if (array_key_exists('perspective', $staticValues['background'])) {
                $area['perspective'] = $staticValues['background']['perspective'];
            }
            if (array_key_exists('layer', $staticValues['background'])) {
                $area['layer'] = $staticValues['background']['layer'];
            }
            $staticValues['area'] = [$area];
            unset($staticValues['background']);
        }

        if (array_key_exists('user', $staticValues)) {
            $staticValues['price'] = [
                'surchargePrices' => $staticValues['user']['surchargePrices'],
                'useSurchargeAsReplacement' => $staticValues['user']['useSurchargeAsReplacement']
            ];
            unset($staticValues['user']['surchargePrices']);
            unset($staticValues['user']['useSurchargeAsReplacement']);

            $staticValues['image'] = $staticValues['user'];
            unset($staticValues['user']);
        }

        if (array_key_exists('text', $staticValues)) {
            $text = $staticValues['text'];

            $text['boxes'] = [[
                // default values because legacy config doesn't provide all of the needed
                'name' => 'Textfeld',
                'identifier' => 'Texteingabe',
                'default' => '',
                'colorPicker' => true,
                'allowMultiple' => false,
                'left' => 700,
                'top' => 700,
                'radius' => 0,
                'locked' => false,
                'maxlength' => 80,
                // mapping of existing values
                'fontSize' => $text['fontSize'],
                'textAlign' => $text['textAlign'],
                'fill' => $text['fill'],
                'multiline' => $text['multiline'],
                'perspective' => $staticValues['area'][0]['perspective']
            ]];
            unset($text['default']);
            unset($text['fontSize']);
            unset($text['textAlign']);
            unset($text['fill']);
            unset($text['multiline']);
            unset($text['fontFamily']);

            $staticValues['text'] = $text;
        }

        if (array_key_exists('image', $staticValues)) {
            $image = $staticValues['image'];
            unset($staticValues['image']);
            $staticValues['image'][$staticValues['area'][0]['perspective']] = $image;
        }

        if (!array_key_exists('motive', $staticValues)) {
            $staticValues['motive'] = [];
        }

        if (!array_key_exists('area', $staticValues)) {
            $staticValues['area']['name'] = 'areaName';
            $staticValues['area']['identifier'] = 'areaId';
        }

        return $staticValues;
    }

    /**
     * @param array $motiveSettings
     * @return array
     */
    private function getMotiveFiles(array $motiveSettings): array
    {
        $files = [];

        if (!array_key_exists('folder', $motiveSettings) || !$motiveSettings['folder']) {
            return $files;
        }

        $directory = new Directory($motiveSettings['folder']);
        foreach ($this->mediaFileSystemConnector->getDirectoryContent($directory) as $content) {
            if (true === $content['isDir']) {
                continue;
            }

            $files[] = $content;
        }

        return $files;
    }
}
