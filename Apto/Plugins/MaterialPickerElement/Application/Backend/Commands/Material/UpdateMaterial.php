<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

class UpdateMaterial extends AbstractAddMaterial
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
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
    public function __construct(string $id, $identifier, array $name, array $description, int $clicks, $previewImage, $reflection, $transmission, $absorption, bool $active, bool $isNotAvailable, int $position, int $conditionsOperator)
    {
        parent::__construct($identifier, $name, $description, $clicks, $previewImage, $reflection, $transmission, $absorption, $active, $isNotAvailable, $position, $conditionsOperator);
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
