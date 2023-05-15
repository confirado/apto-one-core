<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Commands;

use Apto\Base\Application\Core\Commands\AbstractUploadCommand;
use Apto\Base\Application\Core\PublicCommandInterface;

class UploadUserImageFile extends AbstractUploadCommand implements PublicCommandInterface
{
    /**
     * @var string
     */
    protected string $hash;

    /**
     * @var string
     */
    protected string $extension;

    /**
     * @var string
     */
    protected string $path;

    /**
     * @param string $hash
     * @param string $extension
     * @param string $path
     */
    public function __construct(string $hash, string $extension, string $path)
    {
        $this->hash = $hash;
        $this->extension = $extension;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }
}
