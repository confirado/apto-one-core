<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductSectionCustomProperty extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;


    /**
     * @var string
     */
    private $id;

    /**
     * RemoveProductPrice constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $id
     */
    public function __construct(string $productId, string $sectionId, string $id)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
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
    public function getId(): string
    {
        return $this->id;
    }
}
