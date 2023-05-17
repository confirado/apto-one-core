<?php

namespace Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;

class Canvas extends AptoAggregate
{
    /**
     * @var Identifier
     */
    protected $identifier;

    /**
     * @var array
     */
    private $imageSettings;

    /**
     * @var array
     */
    private $textSettings;

    /**
     * @var array
     */
    private $areaSettings;

    /**
     * @var array
     */
    private $priceSettings;

    /**
     * @param AptoUuid $id
     * @param Identifier $identifier
     */
    public function __construct(AptoUuid $id, Identifier $identifier)
    {
        parent::__construct($id);
        $this->identifier = $identifier;

        $this->publish(
            new CanvasAdded(
                $this->getId(),
                $this->getIdentifier()->getValue()
            )
        );

        // set default settings
        $this->imageSettings = [
            'active' => true,
            'previewSize' => 250,
            'maxFileSize' => 4,
            'minWidth' => 0,
            'minHeight' => 0,
            'allowedFileTypes' => ['jpg', 'jpeg', 'png']
        ];

        $this->textSettings = [
            'active' => false,
            'default' => 'Mein Text!',
            'fontSize' => 25,
            'textAlign' => 'center',
            'fill' => '#ffffff',
            'multiline' => false,
            'fonts' => []
        ];

        $this->areaSettings = [
            'image' => null,
            'width' => 1000,
            'height' => 600,
            'perspective' => 'persp1',
            'layer' => '0',
            'area' => [
                'width' => 0,
                'height' => 0,
                'left' => 0,
                'top' => 0
            ]
        ];

        $this->priceSettings = [
            'surchargePrices' => [],
            'useSurchargeAsReplacement' => false
        ];
    }

    /**
     * @return Identifier
     */
    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @param Identifier $identifier
     * @return $this
     */
    public function setIdentifier(Identifier $identifier): self
    {
        if ($this->identifier->equals($identifier)) {
            return $this;
        }

        $this->identifier = $identifier;
        $this->publish(
            new CanvasIdentifierChanged(
                $this->getId(),
                $this->getIdentifier()->getValue()
            )
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getImageSettings(): array
    {
        return $this->imageSettings;
    }

    /**
     * @param array $imageSettings
     * @return $this
     */
    public function setImageSettings(array $imageSettings): self
    {
        $this->imageSettings = $imageSettings;
        return $this;
    }

    /**
     * @return array
     */
    public function getTextSettings(): array
    {
        return $this->textSettings;
    }

    /**
     * @param array $textSettings
     * @return $this
     */
    public function setTextSettings(array $textSettings): self
    {
        $this->textSettings = $textSettings;
        return $this;
    }

    /**
     * @return array
     */
    public function getAreaSettings(): array
    {
        return $this->areaSettings;
    }

    /**
     * @param array $areaSettings
     * @return $this
     */
    public function setAreaSettings(array $areaSettings): self
    {
        $this->areaSettings = $areaSettings;
        return $this;
    }

    /**
     * @return array
     */
    public function getPriceSettings(): array
    {
        return $this->priceSettings;
    }

    /**
     * @param array $priceSettings
     * @return $this
     */
    public function setPriceSettings(array $priceSettings): self
    {
        $this->priceSettings = $priceSettings;
        return $this;
    }
}
