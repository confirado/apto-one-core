<?php

namespace Apto\Base\Application\Core\Query\AptoUuid;

use Apto\Base\Application\Core\PublicQueryInterface;

class GenerateAptoUuid implements PublicQueryInterface
{
    /**
     * @var int
     */
    private $number;

    /**
     * GenerateAptoUuid constructor.
     * @param int|null $number
     */
    public function __construct(int $number = null)
    {
        if (null === $number) {
            $number = 1;
        }

        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }
}