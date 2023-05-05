<?php
namespace Apto\Catalog\Application\Backend\Commands\Filter;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Filter\FilterCategoryRepository;
use Apto\Catalog\Domain\Core\Model\Filter\FilterProperty;
use Apto\Catalog\Domain\Core\Model\Filter\FilterPropertyRemoved;
use Apto\Catalog\Domain\Core\Model\Filter\FilterPropertyRepository;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Doctrine\Common\Collections\ArrayCollection;

class FilterPropertyCommandHandler extends AbstractCommandHandler
{
    /**
     * @var FilterPropertyRepository
     */
    protected $filterPropertyRepository;

    /**
     * @var FilterCategoryRepository
     */
    protected $filterCategoryRepository;

    /**
     * FilterPropertyCommandHandler constructor.
     * @param FilterPropertyRepository $filterPropertyRepository
     * @param FilterCategoryRepository $filterCategoryRepository
     */
    public function __construct(
        FilterPropertyRepository $filterPropertyRepository,
        FilterCategoryRepository $filterCategoryRepository
    ) {
        $this->filterPropertyRepository = $filterPropertyRepository;
        $this->filterCategoryRepository = $filterCategoryRepository;
    }

    /**
     * @param AddFilterProperty $command
     * @throws InvalidUuidException
     * @throws \Exception
     */
    public function handleAddFilterProperty(AddFilterProperty  $command)
    {
        $this->checkUniqueConstraints($command->getIdentifier());

        $filterProperty = new FilterProperty(
            $this->filterPropertyRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName()),
            new Identifier($command->getIdentifier())
        );

        $filterCategories = new ArrayCollection();
        foreach ($command->getFilterCategoryIds() as $category) {
            $filterCategory = $this->filterCategoryRepository->findById($category['id']);
            if ($filterCategory !== null) {
                $filterCategories->add($filterCategory);
            }
        }

        $filterProperty->setFilterCategories($filterCategories);

        $this->filterPropertyRepository->add($filterProperty);
        $filterProperty->publishEvents();
    }

    /**
     * @param RemoveFilterProperty $command
     */
    public function handleRemoveFilterProperty(RemoveFilterProperty $command)
    {
        $filterProperty = $this->filterPropertyRepository->findById($command->getId());

        if(null !== $filterProperty) {
            $this->filterPropertyRepository->remove($filterProperty);
            DomainEventPublisher::instance()->publish(
                new FilterPropertyRemoved(
                    $filterProperty->getId()
                )
            );
        }
    }

    /**
     * @param UpdateFilterProperty $command
     * @throws FilterPropertyIdentifierAlreadyExists
     */
    public function handleUpdateFilterProperty(UpdateFilterProperty $command)
    {
        $filterProperty = $this->filterPropertyRepository->findById($command->getId());

        if (null !== $filterProperty) {
            $this->checkUniqueConstraints($command->getIdentifier(), $command->getId());

            $filterProperty
                ->setName(
                    $this->getTranslatedValue($command->getName())
                )
                ->setIdentifier(
                    new Identifier($command->getIdentifier())
                )
            ;

            $filterCategories = new ArrayCollection();
            foreach ($command->getFilterCategoryIds() as $category) {
                $filterCategory = $this->filterCategoryRepository->findById($category['id']);
                if ($filterCategory !== null) {
                    $filterCategories->add($filterCategory);
                }
            }

            $filterProperty->setFilterCategories($filterCategories);

            $this->filterPropertyRepository->update($filterProperty);
            $filterProperty->publishEvents();
        }
    }

    /**
     * @param string $identifier
     * @param string|null $id
     * @throws FilterPropertyIdentifierAlreadyExists
     */
    private function checkUniqueConstraints(string $identifier, string $id = null)
    {
        $filterPropertyAlreadyExists = $this->filterPropertyRepository->findByIdentifier($identifier);

        if (null !== $filterPropertyAlreadyExists) {
            if (null === $id) {
                throw new FilterPropertyIdentifierAlreadyExists('FilterProperty Identifier already set on FilterProperty width Identifier: ' . $filterPropertyAlreadyExists->getId()->getId() . '.');
            }

            if ($filterPropertyAlreadyExists->getId()->getId() !== $id) {
                throw new FilterPropertyIdentifierAlreadyExists('FilterProperty Identifier already set on FilterProperty width Identifier: ' . $filterPropertyAlreadyExists->getId()->getId() . '.');
            }
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddFilterProperty::class => [
            'method' => 'handleAddFilterProperty',
            'bus' => 'command_bus'
        ];

        yield RemoveFilterProperty::class => [
            'method' => 'handleRemoveFilterProperty',
            'bus' => 'command_bus'
        ];

        yield UpdateFilterProperty::class => [
            'method' => 'handleUpdateFilterProperty',
            'bus' => 'command_bus'
        ];
    }
}