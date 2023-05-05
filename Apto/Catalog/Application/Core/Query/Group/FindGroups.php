<?php

namespace Apto\Catalog\Application\Core\Query\Group;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindGroups implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindGroups constructor.
     * @param string $searchString
     */
    public function __construct(string $searchString = '')
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