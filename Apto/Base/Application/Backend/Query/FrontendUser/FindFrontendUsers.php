<?php

namespace Apto\Base\Application\Backend\Query\FrontendUser;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindFrontendUsers implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * @var bool
     */
    private $secure;

    /**
     * FindUsers constructor.
     * @param string $searchString
     * @param bool $secure
     */
    public function __construct(string $searchString = '', bool $secure = true)
    {
        $this->searchString = $searchString;
        $this->secure = $secure;
    }

    /**
     * @return string
     */
    public function getSearchString(): string
    {
        return $this->searchString;
    }

    /**
     * @return bool
     */
    public function getSecure(): bool
    {
        return $this->secure;
    }
}
