<?php

namespace Apto\Base\Application\Backend\Commands\ContentSnippet;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippet;
use Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippetParentException;
use Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippetRemoved;
use Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippetRepository;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;

class ContentSnippetCommandHandler extends AbstractCommandHandler
{
    /**
     * @var ContentSnippetRepository
     */
    private $contentSnippetRepository;

    /**
     * ContentSnippetCommandHandler constructor.
     * @param ContentSnippetRepository $contentSnippetRepository
     */
    public function __construct(ContentSnippetRepository $contentSnippetRepository)
    {
        $this->contentSnippetRepository = $contentSnippetRepository;
    }

    /**
     * @param AddContentSnippet $command
     * @throws ContentSnippetParentException
     * @throws \Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippetNameException
     */
    public function handleAddContentSnippet(AddContentSnippet $command)
    {
        $content = AptoTranslatedValue::fromArray($command->getContent());
        $parent = $this->contentSnippetRepository->findById($command->getParent());
        $contentSnippet = new ContentSnippet(
            $this->contentSnippetRepository->nextIdentity(),
            $command->getName(),
            $command->getActive(),
            $content,
            $parent,
            $command->getHtml()
        );
        $this->contentSnippetRepository->add($contentSnippet);
        $contentSnippet->publishEvents();
    }

    /**
     * @param UpdateContentSnippet $command
     * @throws ContentSnippetParentException
     * @throws \Apto\Base\Domain\Core\Model\ContentSnippet\ContentSnippetNameException
     */
    public function handleUpdateContentSnippet(UpdateContentSnippet $command)
    {
        $contentSnippet = $this->contentSnippetRepository->findById($command->getId());

        if (null === $contentSnippet) {
            return;
        }

        if (!$this->isContentEmpty($command->getContent())) {
            if ($this->contentSnippetRepository->hasChildren($contentSnippet->getId())) {
                throw new ContentSnippetParentException('Snippet Folder cannot have content');
            }
        }

        $content = AptoTranslatedValue::fromArray($command->getContent());
        $parent = $this->contentSnippetRepository->findById($command->getParent());

        $contentSnippet
            ->setName($command->getName())
            ->setActive($command->getActive())
            ->setParent($parent)
            ->setContent($content)
            ->setHtml($command->getHtml());
        $this->contentSnippetRepository->update($contentSnippet);
        $contentSnippet->publishEvents();
    }

    /**
     * @param RemoveContentSnippet $command
     */
    public function handleRemoveContentSnippet(RemoveContentSnippet $command)
    {
        $contentSnippet = $this->contentSnippetRepository->findById($command->getId());

        if (null === $contentSnippet) {
            return;
        }

        $this->contentSnippetRepository->remove($contentSnippet);
        DomainEventPublisher::instance()->publish(
            new ContentSnippetRemoved(
                $contentSnippet->getId()
            )
        );
    }

    /**
     * @param $content
     * @return bool
     */
    protected function isContentEmpty($content) {
        if (sizeof($content) === 0) {
           return true;
        }
        foreach($content as $locale => $value) {
            if ($value === '0' || !!$value) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddContentSnippet::class => [
            'method' => 'handleAddContentSnippet',
            'bus' => 'command_bus'
        ];

        yield UpdateContentSnippet::class => [
            'method' => 'handleUpdateContentSnippet',
            'bus' => 'command_bus'
        ];

        yield RemoveContentSnippet::class => [
            'method' => 'handleRemoveContentSnippet',
            'bus' => 'command_bus'
        ];
    }
}
