<?php

namespace Apto\Base\Application\Core\Query\ContentSnippet;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindContentSnippet implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * FindContentSnippet constructor.
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