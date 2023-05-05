<?php

namespace Apto\Catalog\Application\Frontend\Query\Product;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindProductConfiguration implements PublicQueryInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string|null
     */
    private ?string $type;

    /**
     * @param string $id
     * @param string|null $type
     */
    public function __construct(string $id, ?string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }
}
