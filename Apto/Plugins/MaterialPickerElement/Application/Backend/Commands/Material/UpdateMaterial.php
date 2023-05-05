<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

class UpdateMaterial extends AbstractAddMaterial
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdateMaterial constructor.
     * @param string $id
     * @param string|null $identifier
     * @param array $name
     * @param array $description
     * @param int $clicks
     * @param string|null $previewImage
     * @param int|null $reflection
     * @param int|null $transmission
     * @param int|null $absorption
     * @param bool $active
     * @param bool $isNotAvailable
     * @param int $position
     */
    public function __construct(string $id, $identifier, array $name, array $description, int $clicks, $previewImage, $reflection, $transmission, $absorption, bool $active, bool $isNotAvailable, int $position)
    {
        parent::__construct($identifier, $name, $description, $clicks, $previewImage, $reflection, $transmission, $absorption, $active, $isNotAvailable, $position);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
