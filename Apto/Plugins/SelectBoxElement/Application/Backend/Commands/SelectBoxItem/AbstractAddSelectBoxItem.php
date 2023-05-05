<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddSelectBoxItem implements CommandInterface
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
    protected $name;

    /**
     * AddSelectBoxItem constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param array $name
     */
    public function __construct(string $productId, string $sectionId, string $elementId, array $name)
    {
        $this->productId = $productId;
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->name = $name;
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
    public function getName(): array
    {
        return $this->name;
    }
}