<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class SetProductSectionAllowMulti extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var bool
     */
    private $allowMulti;

    /**
     * SetProductSectionAllowMulti constructor.
     * @param string $productId
     * @param string $sectionId
     * @param bool $allowMulti
     */
    public function __construct(string $productId, string $sectionId, bool $allowMulti)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->allowMulti = $allowMulti;
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    /**
     * @return bool
     */
    public function getAllowMulti(): bool
    {
        return $this->allowMulti;
    }
}