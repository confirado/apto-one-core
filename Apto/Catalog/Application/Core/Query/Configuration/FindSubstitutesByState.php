<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\PublicQueryInterface;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class FindSubstitutesByState implements PublicQueryInterface
{
    private array $state;

    private string $preResolvedText;

    private string $productId;

    public function __construct(array $state, string $preResolvedText, string $productId)
    {
        $this->state = $state;
        $this->preResolvedText = $preResolvedText;
        $this->productId = $productId;
    }

    public function getState(): array
    {
        return $this->state;
    }

    public function getPreResolvedText(): string
    {
        return $this->preResolvedText;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}
