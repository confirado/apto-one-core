<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

use Apto\Base\Application\Core\CommandInterface;

abstract class RemoveUsage implements CommandInterface
{
    /**
     * @var string
     */
    private $partId;

    /**
     * @var string
     */
    private $usageId;

    /**
     * RemoveProductUsage constructor.
     * @param string $partId
     * @param string $usageId
     */
    public function __construct(string $partId, string $usageId)
    {
        $this->partId = $partId;
        $this->usageId = $usageId;
    }

    /**
     * @return string
     */
    public function getPartId(): string
    {
        return $this->partId;
    }

    /**
     * @return string
     */
    public function getUsageId(): string
    {
        return $this->usageId;
    }
}