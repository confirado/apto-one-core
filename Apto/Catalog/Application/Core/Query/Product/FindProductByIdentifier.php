<?php

namespace Apto\Catalog\Application\Core\Query\Product;


use Apto\Base\Application\Core\PublicQueryInterface;

class FindProductByIdentifier implements PublicQueryInterface
{
    /**
     * @var string
     */
    protected $identifier;

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }


}