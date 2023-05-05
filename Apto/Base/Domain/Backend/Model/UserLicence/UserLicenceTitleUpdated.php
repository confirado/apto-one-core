<?php

namespace Apto\Base\Domain\Backend\Model\UserLicence;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class UserLicenceTitleUpdated extends AbstractDomainEvent
{
    /**
     * @var string
     */
    private $title;

    /**
     * UserLicenceTitleUpdated constructor.
     * @param AptoUuid $id
     * @param string $title
     */
    public function __construct(AptoUuid $id, string $title)
    {
        parent::__construct($id);
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}