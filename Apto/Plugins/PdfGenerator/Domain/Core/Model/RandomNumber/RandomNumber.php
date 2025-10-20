<?php
namespace Apto\Plugins\PdfGenerator\Domain\Core\Model\RandomNumber;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class RandomNumber extends AptoAggregate
{

    /**
     * @phpstan-ignore-next-line
     * @var string
     */
    private $number;

    /**
     * RandomNumber constructor.
     * @param AptoUuid $id
     * @param string $number
     */
    public function __construct(AptoUuid $id, string $number)
    {
        parent::__construct($id);
        $this->publish(
            new RandomNumberAdded(
                $this->getId(),
                $number
            )
        );
        $this->number = $number;
    }
}
