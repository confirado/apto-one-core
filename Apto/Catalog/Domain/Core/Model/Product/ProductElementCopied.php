<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductElementCopied extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $sectionId;

    /**
     * @var AptoUuid
     */
    private $elementId;

    /**
     * @var AptoUuid
     */
    private $copiedElementId;

    /**
     * @var array
     */
    private $entityMapping;

    /**
     * ProductElementCopied constructor.
     * @param AptoUuid $id
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     * @param AptoUuid $copiedElementId
     * @param array $entityMapping
     */
    public function __construct(AptoUuid $id, AptoUuid $sectionId, AptoUuid $elementId, AptoUuid $copiedElementId, array $entityMapping)
    {
        parent::__construct($id);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->copiedElementId = $copiedElementId;
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
    public function getElementId(): AptoUuid
    {
        return $this->elementId;
    }

    /**
     * @return AptoUuid
     */
    public function getCopiedElementId(): AptoUuid
    {
        return $this->copiedElementId;
    }

    /**
     * @return array
     */
    public function getEntityMapping(): array
    {
        return $this->entityMapping;
    }
}