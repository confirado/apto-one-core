<?php

namespace Apto\Plugins\PartsList\Application\Core\Query\Part;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindSections implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindElements constructor.
     * @param string $searchString
     */
    public function __construct(string $searchString)
    {
        $this->searchString = $searchString;
    }

    /**
     * @return string
     */
    public function getSearchString(): string
    {
        return $this->searchString;
    }
}