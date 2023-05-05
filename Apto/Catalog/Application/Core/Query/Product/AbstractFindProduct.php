<?php

namespace Apto\Catalog\Application\Core\Query\Product;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindProduct implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindLanguage constructor.
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