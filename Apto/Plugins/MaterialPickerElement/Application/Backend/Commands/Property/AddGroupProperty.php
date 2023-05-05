<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\CommandInterface;

class AddGroupProperty implements CommandInterface
{
    /**
     * @var string
     */
    private $groupId;

    /**
     * @var array
     */
    private $propertyName;

    /**
     * AddGroupProperty constructor.
     * @param string $groupId
     * @param array $propertyName
     */
    public function __construct(string $groupId, array $propertyName)
    {
        $this->groupId = $groupId;
        $this->propertyName = $propertyName;
    }

    /**
     * @return string
     */
    public function getGroupId(): string
    {
        return $this->groupId;
    }

    /**
     * @return array
     */
    public function getPropertyName(): array
    {
        return $this->propertyName;
    }
}