<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Commands;

use Apto\Base\Application\Core\Commands\AbstractUploadCommand;
use Apto\Base\Application\Core\PublicCommandInterface;

class UploadUserImageFile extends AbstractUploadCommand implements PublicCommandInterface
{
    const TIMESTAMP_MAX_DELTA = 24 * 60 * 60;

    /**
     * @var string
     */
    protected $aptoUuid;

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string
     */
    protected $path;

    /**
     * UploadUserImageFile constructor.
     * @param string $aptoUuid
     * @param int $timestamp
     * @param string $extension
     * @param string $path
     */
    public function __construct(string $aptoUuid, int $timestamp, string $extension, string $path)
    {
        $this->aptoUuid = $aptoUuid;
        $this->timestamp = $timestamp;
        $this->extension = $extension;
        $this->assertValidTimestamp();
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getAptoUuid(): string
    {
        return $this->aptoUuid;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * Timestamp must be within MAX_DELTA seconds around current timestamp
     */
    protected function assertValidTimestamp()
    {
        $now = time();
        if ($this->timestamp < $now - self::TIMESTAMP_MAX_DELTA || $this->timestamp > $now + self::TIMESTAMP_MAX_DELTA) {
            throw new \InvalidArgumentException('timestamp must be within ' . self::TIMESTAMP_MAX_DELTA . ' seconds around current timestamp.');
        }
    }
}