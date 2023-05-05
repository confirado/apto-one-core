<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialPreviewImageRemoved extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $previewImageId;

    /**
     * MaterialPreviewImageRemoved constructor.
     * @param AptoUuid $id
     * @param AptoUuid $previewImageId
     */
    public function __construct(AptoUuid $id, AptoUuid $previewImageId)
    {
        parent::__construct($id);
        $this->previewImageId = $previewImageId;
    }

    /**
     * @return AptoUuid
     */
    public function getPreviewImageId(): AptoUuid
    {
        return $this->previewImageId;
    }
}