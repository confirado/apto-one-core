<?php

namespace Apto\Plugins\ImageUpload\Application\Backend\Commands;

use Exception;
use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas\Canvas;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas\CanvasRemoved;
use Apto\Plugins\ImageUpload\Domain\Core\Model\Canvas\CanvasRepository;

class CanvasCommandHandler implements CommandHandlerInterface
{
    /**
     * @var CanvasRepository
     */
    private $canvasRepository;

    /**
     * @param CanvasRepository $canvasRepository
     */
    public function __construct(CanvasRepository $canvasRepository)
    {
        $this->canvasRepository = $canvasRepository;
    }

    /**
     * @param AddCanvas $command
     * @return void
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleAddCanvas(AddCanvas $command)
    {
        $identifier = new Identifier($command->getIdentifier());
        $this->checkUniqueConstraints($identifier);

        $canvas = new Canvas($this->canvasRepository->nextIdentity(), $identifier);
        $canvas
            ->setImageSettings($command->getImageSettings())
            ->setTextSettings($command->getTextSettings())
            ->setAreaSettings($command->getAreaSettings())
            ->setPriceSettings($command->getPriceSettings())
        ;

        $this->canvasRepository->add($canvas);
        $canvas->publishEvents();
    }

    /**
     * @param UpdateCanvas $command
     * @return void
     * @throws InvalidUuidException
     * @throws Exception
     */
    public function handleUpdateCanvas(UpdateCanvas $command)
    {
        $canvas = $this->canvasRepository->findById(new AptoUuid($command->getId()));

        if (null === $canvas) {
            return;
        }

        $identifier = new Identifier($command->getIdentifier());
        $this->checkUniqueConstraints($identifier, $canvas->getId()->getId());

        $canvas
            ->setIdentifier($identifier)
            ->setImageSettings($command->getImageSettings())
            ->setTextSettings($command->getTextSettings())
            ->setAreaSettings($command->getAreaSettings())
            ->setPriceSettings($command->getPriceSettings())
        ;

        $this->canvasRepository->update($canvas);
        $canvas->publishEvents();
    }

    /**
     * @param RemoveCanvas $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveCanvas(RemoveCanvas $command)
    {
        $canvas = $this->canvasRepository->findById(new AptoUuid($command->getId()));

        if (null === $canvas) {
            return;
        }

        $this->canvasRepository->remove($canvas);
        DomainEventPublisher::instance()->publish(
            new CanvasRemoved(
                $canvas->getId()
            )
        );
    }

    /**
     * @param Identifier $identifier
     * @param string|null $id
     * @throws CanvasIdentifierAlreadyExists
     */
    protected function checkUniqueConstraints(Identifier $identifier, ?string $id = null)
    {
        $groupAlreadyExists = $this->canvasRepository->findByIdentifier($identifier);

        if (null !== $groupAlreadyExists) {
            if (null === $id) {
                throw new CanvasIdentifierAlreadyExists('Group Identifier already set on Group width Id: ' . $groupAlreadyExists->getId()->getId() . '.');
            }

            if ($groupAlreadyExists->getId()->getId() !== $id) {
                throw new CanvasIdentifierAlreadyExists('Group Identifier already set on Group width Id: ' . $groupAlreadyExists->getId()->getId() . '.');
            }
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddCanvas::class => [
            'method' => 'handleAddCanvas',
            'aptoMessageName' => 'ImageUploadAddCanvas',
            'bus' => 'command_bus'
        ];

        yield UpdateCanvas::class => [
            'method' => 'handleUpdateCanvas',
            'aptoMessageName' => 'ImageUploadUpdateCanvas',
            'bus' => 'command_bus'
        ];

        yield RemoveCanvas::class => [
            'method' => 'handleRemoveCanvas',
            'aptoMessageName' => 'ImageUploadRemoveCanvas',
            'bus' => 'command_bus'
        ];
    }
}
