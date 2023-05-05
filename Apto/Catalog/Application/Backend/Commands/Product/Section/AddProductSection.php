<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductSection extends ProductChildCommand
{
    /**
     * @var ?string
     */
    private $sectionIdentifier;

    /**
     * @var array|null
     */
    private $sectionName;

    /**
     * @var bool
     */
    private $active;

    /**
     * AddProductSection constructor.
     * @param string $productId
     * @param string|null $sectionIdentifier
     * @param array|null $sectionName
     * @param false $active
     */
    public function __construct(string $productId, ?string $sectionIdentifier, array $sectionName = null, $active = false)
    {
        parent::__construct($productId);
        $this->sectionIdentifier = $sectionIdentifier;
        $this->sectionName = $sectionName;
        $this->active = $active;
    }

    /**
     * @return string|null
     */
    public function getSectionIdentifier(): ?string
    {
        return $this->sectionIdentifier;
    }

    /**
     * @return array|null
     */
    public function getSectionName()
    {
        return $this->sectionName;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }
}