<?php

namespace Apto\Base\Domain\Backend\Model\UserLicence;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class UserLicenceTextUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $text;

    /**
     * UserLicenceTextUpdated constructor.
     * @param AptoUuid $id
     * @param string $text
     */
    public function __construct(AptoUuid $id, string $text)
    {
        parent::__construct($id);
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}