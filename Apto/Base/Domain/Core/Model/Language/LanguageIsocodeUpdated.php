<?php

namespace Apto\Base\Domain\Core\Model\Language;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class LanguageIsocodeUpdated extends AbstractDomainEvent
{
    /**
     * @var AptoLocale
     */
    private $aptoLocale;

    /**
     * LanguageIsocodeUpdated constructor.
     * @param AptoUuid $id
     * @param AptoLocale $aptoLocale
     */
    public function __construct(AptoUuid $id, AptoLocale $aptoLocale)
    {
        parent::__construct($id);
        $this->aptoLocale = $aptoLocale;
    }

    /**
     * @return AptoLocale
     */
    public function getAptoLocale(): AptoLocale
    {
        return $this->aptoLocale;
    }
}
