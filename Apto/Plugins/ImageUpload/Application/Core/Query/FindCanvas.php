<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCanvas implements PublicQueryInterface
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
