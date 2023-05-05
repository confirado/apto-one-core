<?php

namespace Apto\Catalog\Application\Core\Query\Filter;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindFilterCategory implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindFilterCategory constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}