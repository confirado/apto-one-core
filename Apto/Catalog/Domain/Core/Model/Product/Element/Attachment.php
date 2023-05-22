<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Doctrine\Common\Collections\Collection;

class Attachment extends AptoEntity
{
    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var MediaFile
     */
    protected $mediaFile;

    /**
     * @var Element
     */
    protected $element;

    /**
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     * @param MediaFile $mediaFile
     * @param Element $element
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name, MediaFile $mediaFile, Element $element)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->mediaFile = $mediaFile;
        $this->element = $element;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @return MediaFile
     */
    public function getMediaFile(): MediaFile
    {
        return $this->mediaFile;
    }

    /**
     * @return Element
     */
    public function getElement(): Element
    {
        return $this->element;
    }

    /**
     * @param AptoUuid $id
     * @param Collection $entityMapping
     * @return Attachment
     */
    public function copy(AptoUuid $id, Collection &$entityMapping): Attachment
    {
        // create new attachment
        $attachment = new Attachment(
            $id,
            $this->getName(),
            $this->getMediaFile(),
            $entityMapping->get($this->element->getId()->getId())
        );

        // return new attachment
        return $attachment;
    }
}
