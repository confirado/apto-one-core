<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\File;

use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;

class File
{
    const INVALID_CHARACTERS = [
        "\r", "\n", "\t", '%', ':', ';', '`', '~', '#', '"', "'", '&', '<', '>', '{', '}', '[', ']', '|', '?', '*',  '..', '\\', './', '/'
    ];

    /**
     * @var Directory
     */
    protected $directory;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $extension;


    /**
     * @param string $path
     * @return File
     */
    public static function createFromPath(string $path): File
    {
        $directory = Directory::createFromPath(dirname($path));
        $filename = basename($path);
        return new self($directory, $filename);
    }

    /**
     * File constructor.
     * @param Directory $directory
     * @param string $file
     */
    public function __construct(Directory $directory, string $file)
    {
        $this->directory = $directory;

        // parse filename and extension
        $pos = strrpos($file, '.');
        $filename = false === $pos ? $file : substr($file, 0, $pos);
        $extension = false === $pos ? '' : substr($file, $pos + 1);

        // check filename
        self::assertValidCharacters($file);
        self::assertNameValid($filename);

        $this->filename = $filename;
        $this->extension = $extension;
    }

    /**
     * returns the directory
     * @return Directory
     */
    public function getDirectory(): Directory
    {
        return $this->directory;
    }

    /**
     * returns the filename without extensions and directory
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * returns the file extension, if existing
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * returns a complete path to the file including directory, filename and extension
     * @return string
     */
    public function getPath(): string
    {
        $path = $this->directory->getPath() . $this->filename;
        $path .= '' != $this->extension ? '.' . $this->extension : '';

        return $path;
    }

    /**
     * @param array $validExtensions array of valid extensions formatted lowercase
     * @return bool
     */
    public function hasExtension(array $validExtensions): bool
    {
        $extension = strtolower($this->getExtension());
        return in_array($extension, $validExtensions);
    }

    /**
     * prevent traversal attacks etc. by forbidding special characters
     * @param string $file
     * @throws FileInvalidCharactersException
     */
    public static function assertValidCharacters(string $file)
    {
        // check for defined invalid characters
        foreach (self::INVALID_CHARACTERS as $invalidCharacter) {
            if (false !== strpos($file, $invalidCharacter)) {
                throw new FileInvalidCharactersException($file);
            }
        }

        // check for non ascii characters
        if (preg_match('/[^\x21-\x80]/u', $file)) {
            throw new FileInvalidCharactersException($file);
        }
    }

    /**
     * @param string $filename
     * @throws FileNameInvalidException
     */
    public static function assertNameValid(string $filename)
    {
        if (strlen($filename) == 0) {
            throw new FileNameInvalidException($filename);
        }
    }

    /**
     * @param array $allowedExtensions
     * @throws FileForbiddenExtension
     */
    public function assertHasNotExtension(array $allowedExtensions)
    {
        if ($this->hasExtension($allowedExtensions)) {
            throw new FileForbiddenExtension($this->getPath());
        }
    }

}