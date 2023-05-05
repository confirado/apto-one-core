<?php
namespace Apto\Catalog\Application\Core\Query\Shop;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindShop implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindShop constructor.
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