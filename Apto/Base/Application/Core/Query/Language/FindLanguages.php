<?php

namespace Apto\Base\Application\Core\Query\Language;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindLanguages implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindLanguages constructor.
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