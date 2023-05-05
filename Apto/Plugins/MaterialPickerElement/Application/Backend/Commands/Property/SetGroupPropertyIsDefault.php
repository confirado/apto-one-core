<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Property;

use Apto\Base\Application\Core\CommandInterface;

class SetGroupPropertyIsDefault implements CommandInterface
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
     * @var bool
     */
    private $isDefault;

    /**
     * @param string $groupId
     * @param string $propertyId
     * @param bool $isDefault
     */
    public function __construct(string $groupId, string $propertyId, bool $isDefault)
    {
        $this->groupId = $groupId;
        $this->propertyId = $propertyId;
        $this->isDefault = $isDefault;
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

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}