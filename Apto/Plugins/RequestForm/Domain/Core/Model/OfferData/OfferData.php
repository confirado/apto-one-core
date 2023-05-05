<?php
namespace Apto\Plugins\RequestForm\Domain\Core\Model\OfferData;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class OfferData extends AptoAggregate
{

    /**
     * @var string
     */
    private $number;

    /**
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
