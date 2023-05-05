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
    private $key;

    /**
     * RemoveProductPrice constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $key
     */
    public function __construct(string $productId, string $sectionId, string $key)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->key = $key;
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
    public function getKey(): string
    {
        return $this->key;
    }
}