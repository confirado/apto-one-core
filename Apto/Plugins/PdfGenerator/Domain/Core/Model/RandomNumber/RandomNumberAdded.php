<?php

namespace Apto\Plugins\PdfGenerator\Domain\Core\Model\RandomNumber;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class RandomNumberAdded extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $number;

    /**
     * @param AptoUuid $id
     * @param string $number
     */
    public function __construct(AptoUuid $id, string $number)
    {
        parent::__construct($id);
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }
}
