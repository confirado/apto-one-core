<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem;

use Apto\Base\Application\Core\CommandInterface;

class AddSelectBoxItems implements CommandInterface
{
    /**
     * @var string
     */
    protected $productId;

    /**
     * @var string
     */
    protected $sectionId;

    /**
     * @var string
     */
    protected $elementId;

    /**
     * @var array
     */
    protected $items;

    /**
     * AddSelectBoxItem constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param array $name
     */
    public function __construct(string $productId, string $sectionId, string $elementId, array $items)
    {
        $this->productId = $productId;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
}