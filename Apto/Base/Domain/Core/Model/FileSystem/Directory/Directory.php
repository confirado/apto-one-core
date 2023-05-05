<?php

namespace Apto\Base\Domain\Core\Model\FileSystem\Directory;

class Directory
{
    const INVALID_CHARACTERS = [
        "\r", "\n", "\t", '%', ';', '`', '~', '#', '"', "'", '&', '<', '>', '{', '}', '[', ']', '|', '?', '*', '..', '.\\', './'
    ];

    /**
     * @var string
     */
    protected $path;

    /**
     * @param string $path
     * @return Directory
     */
    public static function createFromPath(string $path): Directory
    {
        return new self($path);
    }

    /**
     * DirectoryName constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        if ('.' == $path) {
            $path = '';
        }
        // fix windows directory separators
        $path = str_replace('\\', '/', $path);
        $path = rtrim($path, '/');
        self::assertValidCharacters($path);

        $this->path = $path;
    }

    /**
     * returns the directory
     * @return string
     */
    public function getPath(): string
    {
        return rtrim($this->path, '/') . '/';
    }

    /**
     * @param string $path
     * @throws DirectoryInvalidCharactersException
     */
    public static function assertValidCharacters(string $path)
    {
        // empty directory, nothing to do
        if ('' == $path) {
            return;
        }

        // check for defined invalid characters
        foreach (self::INVALID_CHARACTERS as $invalidCharacter) {
            if (false !== strpos($path, $invalidCharacter)) {
                throw new DirectoryInvalidCharactersException($path);
            }
        }

        // check for non ascii characters
        $matches = [];
        if (preg_match('/[^\x21-\x80]/u', $path, $matches)) {
            throw new DirectoryInvalidCharactersException($path);
        }
    }

}