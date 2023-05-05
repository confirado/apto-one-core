<?php

namespace Apto\Base\Application\Backend\Commands\MediaFile;

use Apto\Base\Application\Core\CommandInterface;

class RemoveMediaFileByName implements CommandInterface
{

    /**
     * @var string
     */
    protected $file;

    /**
     * RemoveMediaFile constructor.
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