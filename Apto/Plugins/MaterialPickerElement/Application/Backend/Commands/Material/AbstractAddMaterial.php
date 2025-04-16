<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddMaterial implements CommandInterface
{
    /**
     * @var string|null
     */
    private $identifier;

    /**
     * @var array
     */
    private $name;

    /**
     * @var array
     */
    private $description;

    /**
     * @var int
     */
    private $clicks;

    /**
     * @var string|null
     */
    private $previewImage;

    /**
     * @var int|null
     */
    private $reflection;

    /**
     * @var int|null
     */
    private $transmission;

    /**
     * @var int|null
     */
    private $absorption;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var bool
     */
    private $isNotAvailable;

    /**
     * @var int
     */
    private $position;

    /**
     * @var int
     */
    private $conditionsOperator;

    /**
     * @param $identifier
     * @param array $name
     * @param array $description
     * @param int $clicks
     * @param $previewImage
     * @param $reflection
     * @param $transmission
     * @param $absorption
     * @param bool $active
     * @param bool $isNotAvailable
     * @param int $position
     * @param int $conditionsOperator
     */
    public function __construct($identifier, array $name, array $description, int $clicks, $previewImage, $reflection, $transmission, $absorption, bool $active, bool $isNotAvailable, int $position, int $conditionsOperator) {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->description = $description;
        $this->clicks = $clicks;
        $this->previewImage = $previewImage;
        $this->reflection = $reflection;
        $this->transmission = $transmission;
        $this->absorption = $absorption;
        $this->active = $active;
        $this->isNotAvailable = $isNotAvailable;
        $this->position = $position;
        $this->conditionsOperator = $conditionsOperator;
    }

    /**
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getDescription(): array
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getClicks(): int
    {
        return $this->clicks;
    }

    /**
     * @return string|null
     */
    public function getPreviewImage()
    {
        return $this->previewImage;
    }

    /**
     * @return int|null
     */
    public function getReflection()
    {
        return $this->reflection;
    }

    /**
     * @return int|null
     */
    public function getTransmission()
    {
        return $this->transmission;
    }

    /**
     * @return int|null
     */
    public function getAbsorption()
    {
        return $this->absorption;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function getIsNotAvailable(): bool
    {
        return $this->isNotAvailable;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getConditionsOperator(): int
    {
        return $this->conditionsOperator;
    }
}
