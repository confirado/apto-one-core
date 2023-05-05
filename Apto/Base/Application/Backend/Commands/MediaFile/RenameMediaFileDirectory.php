<?php

namespace Apto\Base\Application\Backend\Commands\MediaFile;

use Apto\Base\Application\Core\CommandInterface;

class RenameMediaFileDirectory implements CommandInterface
{

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $newName;

    /**
     * RenameMediaFileDirectory constructor.
     * @param string $directory
     * @param string $newName
     */
    public function __construct(string $directory, string $newName)
    {
        $this->directory = $directory;
        $this->newName = $newName;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }

}