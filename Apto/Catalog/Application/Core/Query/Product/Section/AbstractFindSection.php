<?php

namespace Apto\Catalog\Application\Core\Query\Product\Section;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindSection implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * FindSection constructor.
     * @param string $sectionId
     */
    public function __construct(string $sectionId)
    {
        $this->sectionId = $sectionId;
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }
}