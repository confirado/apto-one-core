<?php

namespace Apto\Base\Application\Backend\Commands\MediaFile;

use Apto\Base\Application\Core\CommandInterface;

class AddMediaFileDirectory implements CommandInterface
{

    /**
     * @var string
     */
    protected $directory;

    /**
     * AddMediaFileDirectory constructor.
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

}