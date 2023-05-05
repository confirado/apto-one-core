<?php
namespace Apto\Catalog\Domain\Core\Model\Filter;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class FilterProperty extends AptoAggregate
{
    use AptoCustomProperties;

    /**
     * @var AptoTranslatedValue
     */
    protected $name;

    /**
     * @var Identifier
     */
    protected $identifier;

    /**
     * @var Collection
     */
    protected $filterCategories;

    /**
     * FilterProperty constructor.
     * @param AptoUuid $id
     * @param AptoTranslatedValue $name
     * @param Identifier $identifier
     */
    public function __construct(
        AptoUuid $id,
        AptoTranslatedValue $name,
        Identifier $identifier
    ) {
        parent::__construct($id);
        $this->publish(
            new FilterPropertyAdded(
                $this->getId()
            )
        );
        $this->setName($name);
        $this->identifier = $identifier;
        $this->filterCategories = new ArrayCollection();
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getName(): AptoTranslatedValue
    {
        return $this->name;
    }

    /**
     * @param AptoTranslatedValue $name
     * @return FilterProperty
     */
    public function setName(AptoTranslatedValue $name)
    {
        if (null !== $this->name && $this->name->equals($name)) {
            return $this;
        }
        $this->name = $name;
        $this->publish(
            new FilterPropertyNameUpdated(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @return Identifier
     */
    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @param Identifier $identifier
     * @return FilterProperty
     */
    public function setIdentifier(Identifier $identifier)
    {
        if ($this->identifier->equals($identifier)) {
            return $this;
        }

        $this->identifier = $identifier;
        $this->publish(
            new FilterPropertyIdentifierUpdated(
                $this->getId(),
                $this->identifier->getValue()
            )
        );
        return $this;
    }

    /**
     * @return Collection
     */
    public function getFilterCategories(): Collection
    {
        return $this->filterCategories;
    }

    /**
     * @param Collection $filterCategories
     * @return FilterProperty
     */
    public function setFilterCategories(Collection $filterCategories): FilterProperty
    {
        if ($this->filterCategories !== null && !$this->hasCollectionChanged($this->getFilterCategories(), $filterCategories)) {
            return $this;
        }
        $this->filterCategories = $filterCategories;
        $this->publish(
            new FilterPropertyFilterCategoriesUpdated(
                $this->getId(),
                $this->getCollectionIds($this->getFilterCategories())
            )
        );
        return $this;
    }
}