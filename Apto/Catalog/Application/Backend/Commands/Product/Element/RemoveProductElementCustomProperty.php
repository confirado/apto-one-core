<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductElementCustomProperty extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $elementId;

    /**
     * @var string
     */
    private $id;

    /**
     * RemoveProductPrice constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param string $id
     */
    public function __construct(string $productId, string $sectionId, string $elementId, string $id)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->id = $id;
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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
