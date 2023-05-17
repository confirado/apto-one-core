<?php

namespace Apto\Plugins\ImageUpload\Application\Backend\Commands;

abstract class AbstractAddCanvas
{
    /**
     * @var string
     */
    private $identifier;

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
     * @param string $identifier
     * @param array $imageSettings
     * @param array $textSettings
     * @param array $areaSettings
     * @param array $priceSettings
     */
    public function __construct(string $identifier, array $imageSettings, array $textSettings, array $areaSettings, array $priceSettings)
    {
        $this->identifier = $identifier;
        $this->imageSettings = $imageSettings;
        $this->textSettings = $textSettings;
        $this->areaSettings = $areaSettings;
        $this->priceSettings = $priceSettings;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return array
     */
    public function getImageSettings(): array
    {
        return $this->imageSettings;
    }

    /**
     * @return array
     */
    public function getTextSettings(): array
    {
        return $this->textSettings;
    }

    /**
     * @return array
     */
    public function getAreaSettings(): array
    {
        return $this->areaSettings;
    }

    /**
     * @return array
     */
    public function getPriceSettings(): array
    {
        return $this->priceSettings;
    }
}
