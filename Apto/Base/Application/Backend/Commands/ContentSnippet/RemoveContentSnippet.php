<?php

namespace Apto\Base\Application\Backend\Commands\ContentSnippet;

use Apto\Base\Application\Core\CommandInterface;

class RemoveContentSnippet implements CommandInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * ContentSnippet constructor.
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