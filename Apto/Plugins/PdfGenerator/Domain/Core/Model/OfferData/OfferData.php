<?php
namespace Apto\Plugins\PdfGenerator\Domain\Core\Model\OfferData;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class OfferData extends AptoAggregate
{

    /**
     * @phpstan-ignore-next-line
     * @var string
     */
    private $number;

    /**
     * @phpstan-ignore-next-line
     * @var string
     */
    private $data;

    /**
     * @param AptoUuid $id
     * @param string $number
     * @param string $data
     */
    public function __construct(AptoUuid $id, string $number, string $data)
    {
        parent::__construct($id);
        $this->publish(
            new OfferDataAdded(
                $this->getId()
            )
        );
        $this->number = $number;
        $this->data = $data;
    }
}
