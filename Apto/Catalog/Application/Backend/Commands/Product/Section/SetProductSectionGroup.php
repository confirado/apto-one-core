<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class SetProductSectionGroup extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $groupId;

    /**
     * SetProductSectionGroup constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $groupId
     */
    public function __construct(string $productId, string $sectionId, string $groupId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->groupId = $groupId;
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
    public function getGroupId(): string
    {
        return $this->groupId;
    }
}