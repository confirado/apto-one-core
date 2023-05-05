<?php
namespace Apto\Catalog\Application\Backend\Commands\Filter;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Catalog\Domain\Core\Model\Filter\FilterCategory;
use Apto\Catalog\Domain\Core\Model\Filter\FilterCategoryRemoved;
use Apto\Catalog\Domain\Core\Model\Filter\FilterCategoryRepository;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;

class FilterCategoryCommandHandler extends AbstractCommandHandler
{
    /**
     * @var FilterCategoryRepository
     */
    protected $filterCategoryRepository;

    /**
     * FilterPropertyCommandHandler constructor.
     * @param FilterCategoryRepository $filterCategoryRepository
     */
    public function __construct(
        FilterCategoryRepository $filterCategoryRepository
    ) {
        $this->filterCategoryRepository = $filterCategoryRepository;
    }

    /**
     * @param AddFilterCategory $command
     * @throws \Exception
     */
    public function handleAddFilterCategory(AddFilterCategory  $command)
    {
        $this->checkUniqueConstraints($command->getIdentifier());

        $filterCategory = new FilterCategory(
            $this->filterCategoryRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName()),
            new Identifier($command->getIdentifier())
        );

        $this->filterCategoryRepository->add($filterCategory);
        $filterCategory->publishEvents();
    }

    /**
     * @param RemoveFilterCategory $command
     */
    public function handleRemoveFilterCategory(RemoveFilterCategory $command)
    {
        $filterCategory = $this->filterCategoryRepository->findById($command->getId());

        if(null !== $filterCategory) {
            $this->filterCategoryRepository->remove($filterCategory);
            DomainEventPublisher::instance()->publish(
                new FilterCategoryRemoved(
                    $filterCategory->getId()
                )
            );
        }
    }

    /**
     * @param UpdateFilterCategory $command
     * @throws FilterCategoryIdentifierAlreadyExists
     * @throws \Exception
     */
    public function handleUpdateFilterCategory(UpdateFilterCategory $command)
    {
        $filterCategory = $this->filterCategoryRepository->findById($command->getId());

        if (null !== $filterCategory) {
            $this->checkUniqueConstraints($command->getIdentifier(), $command->getId());

            $filterCategory
                ->setName(
                    $this->getTranslatedValue($command->getName())
                )
                ->setIdentifier(
                    new Identifier($command->getIdentifier())
                )
                ->setPosition(
                    $command->getPosition())
            ;

            $this->filterCategoryRepository->update($filterCategory);
            $filterCategory->publishEvents();
        }
    }

    /**
     * @param string $identifier
     * @throws FilterCategoryIdentifierAlreadyExists
     */
    private function checkUniqueConstraints(string $identifier, string $id ="")
    {
        $filterCategoryAlreadyExists = $this->filterCategoryRepository->findByIdentifier($identifier);

        if (null !== $filterCategoryAlreadyExists && $id !== $filterCategoryAlreadyExists->getId()->getId()) {
            if (null === $identifier) {
                throw new FilterCategoryIdentifierAlreadyExists('FilterCategory Identifier already set on FilterCategory with Identifier: ' . $filterCategoryAlreadyExists->getId()->getId() . '.');
            }

            if ($filterCategoryAlreadyExists->getId()->getId() !== $identifier) {
                throw new FilterCategoryIdentifierAlreadyExists('FilterCategory Identifier already set on FilterCategory with Identifier: ' . $filterCategoryAlreadyExists->getId()->getId() . '.');
            }
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddFilterCategory::class => [
            'method' => 'handleAddFilterCategory',
            'bus' => 'command_bus'
        ];

        yield RemoveFilterCategory::class => [
            'method' => 'handleRemoveFilterCategory',
            'bus' => 'command_bus'
        ];

        yield UpdateFilterCategory::class => [
            'method' => 'handleUpdateFilterCategory',
            'bus' => 'command_bus'
        ];
    }
}