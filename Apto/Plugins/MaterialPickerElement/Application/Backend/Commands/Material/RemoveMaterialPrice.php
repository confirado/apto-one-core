<?php
namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class RemoveMaterialPrice implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $priceId;

    /**
     * RemoveProductPrice constructor.
     * @param string $id
     * @param string $priceId
     */
    public function __construct(string $id, string $priceId)
    {
        $this->id = $id;
        $this->priceId = $priceId;
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
    public function getPriceId(): string
    {
        return $this->priceId;
    }
}