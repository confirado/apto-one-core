<?php

namespace Apto\Catalog\Application\Core\Query\Product;

abstract class AbstractFindSectionIdByIdentifier extends AbstractFindProductIdByIdentifier
{
    /**
     * @var string
     */
    protected $sectionIdentifier;

    /**
     * FindSectionIdByIdentifier constructor.
     * @param string $productIdentifier
     * @param string $sectionIdentifier
     */
    public function __construct(string $productIdentifier, string $sectionIdentifier)
    {
        parent::__construct($productIdentifier);
        $this->sectionIdentifier = $sectionIdentifier;
    }

    /**
     * @return string
     */
    public function getSectionIdentifier(): string
    {
        return $this->sectionIdentifier;
    }
}