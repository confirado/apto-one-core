<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\CommandInterface;

class RemovePropertyCustomProperty implements CommandInterface
{
    /**
     * @var string
     */
    private $propertyId;

    /**
     * @var string
     */
    private $id;

    /**
     * RemovePropertyCustomProperty constructor.
     * @param string $propertyId
     * @param string $id
     */
    public function __construct(string $propertyId, string $id)
    {
        $this->propertyId = $propertyId;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPropertyId(): string
    {
        return $this->propertyId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
