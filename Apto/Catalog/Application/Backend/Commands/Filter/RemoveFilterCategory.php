<?php

namespace Apto\Catalog\Application\Backend\Commands\Filter;

use Apto\Base\Application\Core\CommandInterface;

class RemoveFilterCategory implements CommandInterface
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