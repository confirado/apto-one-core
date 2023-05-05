<?php

namespace Apto\Catalog\Application\Core\Query\Product;

class FindElementIdByIdentifier extends AbstractFindSectionIdByIdentifier
{
    /**
     * @var string
     */
    protected $elementIdentifier;

    /**
     * FindElementIdByIdentifier constructor.
     * @param string $productIdentifier
     * @param string $sectionIdentifier
     * @param string $elementIdentifier
     */
    public function __construct(string $productIdentifier, string $sectionIdentifier, string $elementIdentifier)
    {
        parent::__construct($productIdentifier, $sectionIdentifier);
        $this->elementIdentifier = $elementIdentifier;
    }

    /**
     * @return string
     */
    public function getElementIdentifier(): string
    {
        return $this->elementIdentifier;
    }
}