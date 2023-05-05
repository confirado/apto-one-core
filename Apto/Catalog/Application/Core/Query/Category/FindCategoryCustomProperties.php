<?php

namespace Apto\Catalog\Application\Core\Query\Category;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCategoryCustomProperties implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindCategory constructor.
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