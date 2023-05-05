<?php

namespace Apto\Base\Application\Core\Query\MediaFile;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindMediaFileByName implements PublicQueryInterface
{
    /**
     * @var string
     */
    protected $file;

    /**
     * FindMediaFile constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
}