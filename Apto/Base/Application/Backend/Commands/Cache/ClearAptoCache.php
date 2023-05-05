<?php

namespace Apto\Base\Application\Backend\Commands\Cache;

use Apto\Base\Application\Core\CommandInterface;

class ClearAptoCache implements CommandInterface
{

    /**
     * @var array
     */
    protected $types;

    /**
     * @param array $types
     */
    public function __construct(array $types = ['image-rendered', 'image-thumb', 'apcu'])
    {
        $this->types = $types;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}