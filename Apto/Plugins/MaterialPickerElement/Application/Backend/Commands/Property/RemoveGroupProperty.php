<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\CommandInterface;

class RemoveGroupProperty implements CommandInterface
{
    /**
     * @var string
     */
    private $groupId;

    /**
     * @var string
     */
    private $propertyId;

    /**
     * RemoveGroupProperty constructor.
     * @param string $groupId
     * @param string $propertyId
     */
    public function __construct(string $groupId, string $propertyId)
    {
        $this->groupId = $groupId;
        $this->propertyId = $propertyId;
    }

    /**
     * @return string
     */
    public function getGroupId(): string
    {
        return $this->groupId;
    }

    /**
     * @return string
     */
    public function getPropertyId(): string
    {
        return $this->propertyId;
    }
}