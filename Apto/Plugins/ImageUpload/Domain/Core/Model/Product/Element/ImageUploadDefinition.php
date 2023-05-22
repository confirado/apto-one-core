<?php

namespace Apto\Plugins\ImageUpload\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementBoolValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinitionDefaultValues;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementJsonValue;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Service\Converter\MimeTypeExtensionConverter;

class ImageUploadDefinition implements ElementDefinition, ElementDefinitionDefaultValues
{
    const NAME = 'Image Upload Element';
    const BACKEND_COMPONENT = '<apto-image-upload-element definition-validation="setDefinitionValidation(definitionValidation)" product-id="productId" section-id="sectionId" element="detail"></apto-image-upload-element>';
    const FRONTEND_COMPONENT = '<apto-image-upload-element section-ctrl="$ctrl.section" section="section" element="element"></apto-image-upload-element>';

    /**
     * @var array
     */
    protected $canvas;

    /**
     * @var array
     */
    protected $user;

    /**
     * @var array
     */
    protected $background;

    /**
     * @var array
     */
    protected $text;

    /**
     * @param array $canvas
     * @param array $user
     * @param array $background
     * @param array $text
     */
    public function __construct(array $canvas, array $user, array $background, array $text)
    {
        $this->canvas = $canvas;
        $this->user = $user;
        $this->background = $background;
        $this->text = $text;
    }

    /**
     * @return array
     */
    public function getSelectableValues(): array
    {
        return [
            'aptoElementDefinitionId' => new ElementValueCollection([new ElementJsonValue()]),
            'payload' => new ElementValueCollection([new ElementJsonValue()])
        ];
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return [
            'aptoElementDefinitionId' => 'apto-element-image-upload',
            'payload' => null
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
            'aptoElementDefinitionId' => 'apto-element-image-upload',
            'canvas' => $this->canvas,
            'user' => $this->user,
            'background' => $this->background,
            'text' => $this->text
        ];
    }

    /**
     * @param array $selectedValues
     * @return array
     */
    public function getHumanReadableValues(array $selectedValues): array
    {
        return [];
        /*$fabricItemsOnCanvas = $selectedValues['fabricItemsOnCanvas'];
        if (!is_array($fabricItemsOnCanvas)) {
            return [];
        }
        $renderImage = null;
        $payload = $selectedValues['payload'];
        if (isset($payload['renderImageURL'])) {
            $result['renderImage'] =
                AptoTranslatedValue::fromArray([
                    'de_DE' => 'Render Bild: ' . $payload['renderImageURL'],
                    'en_EN' => 'Render image: ' . $payload['renderImageURL'],
                ]);
        }

        $humanReadableImages = [
            'de_DE' => 'Bild: Nein',
            'en_EN' => 'Image: No'
        ];
        $human2DReadableText = [
            'de_DE' => 'Text: Nein',
            'en_EN' => 'Text: No'
        ];

        if ($this->hasStickAreaItemsOnCanvas($fabricItemsOnCanvas, 'image')) {
            $humanReadableImages = [
                'de_DE' => 'Bild: Ja',
                'en_EN' => 'image: Yes'
            ];
        }

        if ($this->hasStickAreaItemsOnCanvas($fabricItemsOnCanvas, 'text')) {
            $human2DReadableText = [
                'de_DE' => 'Text: Ja',
                'en_EN' => 'Text: Yes'
            ];
        }

        $result['UserImage'] = AptoTranslatedValue::fromArray($humanReadableImages);
        $result['UserText'] = AptoTranslatedValue::fromArray($human2DReadableText);

        return $result;*/
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
                'canvas' => $this->canvas,
                'user' => $this->user,
                'background' => $this->background,
                'text' => $this->text
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
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ImageUploadDefinition\' due to wrong class namespace.');
        }

        if (!isset($json['json']['canvas'])) {
            $json['json']['canvas'] = [
                'source' => 'Element',
                'canvasId' => null
            ];
        }

        if ($json['json']['canvas']['source'] === 'Global' && null === $json['json']['canvas']['canvasId']) {
            $json['json']['canvas']['source'] = 'Element';
        }

        if (!isset($json['json']['user'])) {
            $json['json']['user'] = [
                'previewSize' => '250',
                'maxFileSize' => '4',
                'minWidth' => 0,
                'minHeight' => 0,
                'allowedFileTypes' => ['jpg', 'jpeg', 'png']
            ];
        }

        if (!isset($json['json']['user']['useSurchargeAsReplacement'])) {
            $json['json']['user']['useSurchargeAsReplacement'] = false;
        }

        if (!isset($json['json']['user']['surchargePrices'])) {
            $json['json']['user']['surchargePrices'] = [];
        }

        if (!isset($json['json']['background'])) {
            $json['json']['background'] = [
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

        if (!isset($json['json']['text'])) {
            $json['json']['text'] = [
                'active' => false,
                'options' => []
            ];
        }

        if (!isset($json['json']['text']['default'])) {
            $json['json']['text']['default'] = 'Mein Text!';
        }

        if (!isset($json['json']['text']['fontSize'])) {
            $json['json']['text']['fontSize'] = 25;
        }

        if (!isset($json['json']['text']['fontFamily'])) {
            $json['json']['text']['fontFamily'] = 'Open Sans';
        }

        if (!isset($json['json']['text']['textAlign'])) {
            $json['json']['text']['textAlign'] = 'center';
        }

        if (!isset($json['json']['text']['fill'])) {
            $json['json']['text']['fill'] = '#ffffff';
        }

        if (!isset($json['json']['text']['multiline'])) {
            $json['json']['text']['multiline'] = false;
        }

        if (!isset($json['json']['text']['fonts'])) {
            $json['json']['text']['fonts'] = [];
        }

        // add mime types
        $mimeTypeExtensionConverter = new MimeTypeExtensionConverter();
        $json['json']['user']['allowedMimeTypes'] = $mimeTypeExtensionConverter->extensionsToMimeTypes(
            $json['json']['user']['allowedFileTypes']
        );

        return new self(
            $json['json']['canvas'],
            $json['json']['user'],
            $json['json']['background'],
            $json['json']['text']
        );
    }

    /**
     * @param array $fabricItemsOnCanvas
     * @param string $type
     * @return bool

    private function hasStickAreaItemsOnCanvas(array $fabricItemsOnCanvas, string $type): bool
    {
        if (count($fabricItemsOnCanvas[$type]) > 0) {
            return true;
        }
        return false;
    }*/
}
