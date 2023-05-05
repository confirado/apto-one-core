<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPoolPropertyGroups implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $poolId;

    /**
     * FindPoolPriceGroups constructor.
     * @param string $poolId
     */
    public function __construct(string $poolId)
    {
        $this->poolId = $poolId;
    }

    /**
     * @return string
     */
    public function getPoolId(): string
    {
        return $this->poolId;
    }
}