<?php

namespace Apto\Plugins\ImageUpload\Application\Backend\Commands;

use Apto\Base\Application\Core\CommandInterface;

class RemoveCanvas implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
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
