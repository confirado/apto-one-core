<?php
namespace Apto\Catalog\Domain\Core\Model\Category;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Doctrine\Common\Collections\ArrayCollection;

class Category extends AptoAggregate
{
    use AptoCustomProperties;
    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var AptoTranslatedValue
     */
    protected $description;

    /**
     * @var Category|null
     */
    protected $parent;

    /**
     * @var int
     */
    protected $parentId;

    /**
     * @var MediaFile
     */
    protected $previewImage;

    /**
     * @var integer
     */
    protected $position;

    /**
     * Category constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     * @param int $position
     * @param Category|null $parent
     * @throws CategoryParentException
     */
    public function __construct(AptoUuid $id, AptoTranslatedValue $name, int $position, ?Category $parent = null)
    {
        parent::__construct($id);
        $this->publish(
            new CategoryAdded(
                $this->getId()
            )
        );
        $this->customProperties = new ArrayCollection();
        $this->setName($name);
        $this->setParent($parent);
        $this->previewImage = null;
        $this->position = $position;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param AptoTranslatedValue $name
     * @return Category
     */
    public function setName(AptoTranslatedValue $name)
    {
        if (null !== $this->name && $this->name->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new CategoryNameUpdated(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param AptoTranslatedValue $description
     * @return Category
     */
    public function setDescription(AptoTranslatedValue $description)
    {
        if (null !== $this->description && $this->description->equals($description)) {
            return $this;
        }
        $this->description = $description;
        $this->publish(
            new CategoryDescriptionUpdated(
                $this->getId(),
                $this->getDescription()
            )
        );
        return $this;
    }

    /**
     * @return Category|null
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @param Category|null $parent
     * @return Category
     * @throws CategoryParentException
     */
    public function setParent(?Category $parent = null): Category
    {
        // case parent to set equals null
        if (null === $parent) {
            if (null === $this->parent) {
                return $this;
            }

            $this->parent = $parent;
            $this->publish(
                new CategoryParentChanged(
                    $this->getId(),
                    $parent
                )
            );
            return $this;
        }

        // case parent to set is of type Category
        // parent equals current parent
        if (null !== $this->parent && $this->parent->getId()->getId() === $parent->getId()->getId()) {
            return $this;
        }

        // assert valid content snippet
        if ($parent->getId() === $this->getId()) {
            throw new CategoryParentException('Parent cannot be set to self!');
        }

        // change parent
        $this->parent = $parent;
        $this->publish(
            new CategoryParentChanged(
                $this->getId(),
                $parent
            )
        );
        return $this;
    }

    /**
     * @param MediaFile|null $previewImage
     * @return Category
     */
    public function setPreviewImage(MediaFile $previewImage = null): Category
    {
        $this->previewImage = $previewImage;
        return $this;
    }

    /**
     * @return MediaFile|null
     */
    public function getPreviewImage()
    {
        return $this->previewImage;
    }

    /**
     * @return Category
     */
    public function removePreviewImage(): Category
    {
        $this->previewImage = null;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return Category
     */
    public function setPosition(int $position): Category
    {
        $this->position = $position;
        return $this;
    }
}