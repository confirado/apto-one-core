<?php

namespace Apto\Catalog\Application\Core\Query\Group;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindGroup implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindGroup constructor.
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