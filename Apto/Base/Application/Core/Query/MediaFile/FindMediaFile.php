<?php

namespace Apto\Base\Application\Core\Query\MediaFile;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindMediaFile implements PublicQueryInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * FindMediaFile constructor.
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