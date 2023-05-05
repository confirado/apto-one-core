<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem;

use Apto\Base\Application\Core\CommandInterface;

class SetSelectBoxItemIsDefault implements CommandInterface
{
    /**
     * @var string
     */
    protected $elementId;

    /**
     * @var string
     */
    protected $selectBoxItemId;

    /**
     * @var bool
     */
    protected $isDefault;

    /**
     * SetSelectBoxItemIsDefault constructor.
     * @param string $elementId
     * @param string $selectBoxItemId
     * @param bool $isDefault
     */
    public function __construct(string $elementId, string $selectBoxItemId, bool $isDefault)
    {
        $this->elementId = $elementId;
        $this->selectBoxItemId = $selectBoxItemId;
        $this->isDefault = $isDefault;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return string
     */
    public function getSelectBoxItemId(): string
    {
        return $this->selectBoxItemId;
    }

    /**
     * @return bool
     */
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }
}