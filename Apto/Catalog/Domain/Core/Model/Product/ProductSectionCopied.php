<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductSectionCopied extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $sectionId;

    /**
     * @var AptoUuid
     */
    private $copiedSectionId;

    /**
     * @var array
     */
    private $entityMapping;

    /**
     * ProductSectionCopied constructor.
     * @param AptoUuid $id
     * @param AptoUuid $sectionId
     * @param AptoUuid $copiedSectionId
     * @param array $entityMapping
     */
    public function __construct(AptoUuid $id, AptoUuid $sectionId, AptoUuid $copiedSectionId, array $entityMapping)
    {
        parent::__construct($id);
        $this->sectionId = $sectionId;
        $this->copiedSectionId = $copiedSectionId;
        $this->entityMapping = $entityMapping;
    }

    /**
     * @return AptoUuid
     */
    public function getSectionId(): AptoUuid
    {
        return $this->sectionId;
    }

    /**
     * @return AptoUuid
     */
    public function getCopiedSectionId(): AptoUuid
    {
        return $this->copiedSectionId;
    }

    /**
     * @return array
     */
    public function getEntityMapping(): array
    {
        return $this->entityMapping;
    }
}