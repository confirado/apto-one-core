<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\CommandInterface;

class RemovePriceMatrix implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * RemovePriceMatrix constructor.
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