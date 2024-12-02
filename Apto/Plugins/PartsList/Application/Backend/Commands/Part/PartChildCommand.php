<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

use Apto\Base\Application\Core\CommandInterface;

abstract class PartChildCommand implements CommandInterface
{
    /**
     * @var string
     */
    private $partId;

    /**
     * @param string $partId
     */
    public function __construct(string $partId)
    {
        $this->partId = $partId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->partId;
    }
}
