<?php

namespace Apto\Base\Application\Backend\Query\MediaFile;

use Apto\Base\Application\Core\QueryInterface;

class ListMediaFiles implements QueryInterface
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var array
     */
    protected $allowedExtensions;

    /**
     * ListMediaFiles constructor.
     * @param string $directory
     * @param array $allowedExtensions
     */
    public function __construct(string $directory = '', array $allowedExtensions = [])
    {
        $this->directory = $directory;
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return array
     */
    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

}