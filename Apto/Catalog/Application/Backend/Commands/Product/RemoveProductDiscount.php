<?php
namespace Apto\Catalog\Application\Backend\Commands\Product;

use Apto\Base\Application\Core\CommandInterface;

class RemoveProductDiscount implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $discountId;

    /**
     * RemoveProductDiscount constructor.
     * @param string $id
     * @param string $discountId
     */
    public function __construct(string $id, string $discountId)
    {
        $this->id = $id;
        $this->discountId = $discountId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDiscountId(): string
    {
        return $this->discountId;
    }
}