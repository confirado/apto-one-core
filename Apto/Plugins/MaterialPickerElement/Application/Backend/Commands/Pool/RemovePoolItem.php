<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Pool;

use Apto\Base\Application\Core\CommandInterface;

class RemovePoolItem implements CommandInterface
{
    /**
     * @var string
     */
    private $poolId;

    /**
     * @var string
     */
    private $poolItemId;

    /**
     * RemovePoolItem constructor.
     * @param string $poolId
     * @param string $poolItemId
     */
    public function __construct(string $poolId, string $poolItemId)
    {
        $this->poolId = $poolId;
        $this->poolItemId = $poolItemId;
    }

    /**
     * @return string
     */
    public function getPoolId(): string
    {
        return $this->poolId;
    }

    /**
     * @return string
     */
    public function getPoolItemId(): string
    {
        return $this->poolItemId;
    }
}