<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

use Apto\Base\Application\Core\CommandInterface;

class RemoveProduct implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * RemoveProduct constructor.
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