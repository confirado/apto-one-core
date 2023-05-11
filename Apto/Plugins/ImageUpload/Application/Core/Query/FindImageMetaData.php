<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindImageMetaData implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * FindImageMetaData constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
