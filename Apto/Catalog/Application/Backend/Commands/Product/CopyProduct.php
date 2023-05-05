<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

use Apto\Base\Application\Core\CommandInterface;

class CopyProduct implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * CopyProduct constructor.
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