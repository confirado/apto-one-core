<?php

namespace Apto\Catalog\Application\Core\Commands\Configuration;

use Apto\Base\Application\Core\PublicCommandInterface;

class RemoveBasketConfiguration implements PublicCommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * RemoveBasketConfiguration constructor.
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