<?php

namespace Apto\Base\Application\Backend\Commands\MediaFile;

use Apto\Base\Application\Core\CommandInterface;

class RemoveMediaFile implements CommandInterface
{

    /**
     * @var string
     */
    protected $id;

    /**
     * RemoveMediaFile constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

}