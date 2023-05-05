<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class MaterialGalleryImageAdded extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $galleryImageId;

    /**
     * MaterialGalleryImageAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $galleryImageId
     */
    public function __construct(AptoUuid $id, AptoUuid $galleryImageId)
    {
        parent::__construct($id);
        $this->galleryImageId = $galleryImageId;
    }

    /**
     * @return AptoUuid
     */
    public function getGalleryImageId(): AptoUuid
    {
        return $this->galleryImageId;
    }
}