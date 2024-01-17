<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductSection extends ProductChildCommand
{
    /**
     * @var string|null
     */
    private ?string $sectionIdentifier;

    /**
     * @var array|null
     */
    private ?array$sectionName;

    /**
     * @var bool
     */
    private bool $active;

    /**
     * @var bool
     */
    private bool $addDefaultElement;

    /**
     * @param string $productId
     * @param string|null $sectionIdentifier
     * @param array|null $sectionName
     * @param bool $active
     * @param bool $addDefaultElement
     */
    public function __construct(string $productId, ?string $sectionIdentifier, array $sectionName = null, bool $active = false, bool $addDefaultElement = false)
    {
        parent::__construct($productId);
        $this->sectionIdentifier = $sectionIdentifier;
        $this->sectionName = $sectionName;
        $this->active = $active;
        $this->addDefaultElement = $addDefaultElement;
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
    public function getSectionName(): ?array
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

    /**
     * @return bool
     */
    public function getAddDefaultElement(): bool
    {
        return $this->addDefaultElement;
    }
}
