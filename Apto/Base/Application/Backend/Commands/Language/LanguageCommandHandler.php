<?php

namespace Apto\Base\Application\Backend\Commands\Language;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\Language\Language;
use Apto\Base\Domain\Core\Model\Language\LanguageRemoved;
use Apto\Base\Domain\Core\Model\Language\LanguageRepository;

class LanguageCommandHandler extends AbstractCommandHandler
{
    /**
     * @var LanguageRepository
     */
    protected $languageRepository;

    /**
     * AddLanguageHandler constructor.
     * @param LanguageRepository $languageRepository
     */
    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param AddLanguage $command
     */
    public function handleAddLanguage(AddLanguage $command)
    {
        $existingLanguage = $this->languageRepository->findOneByIsocode($command->getIsocode());

        if (null !== $existingLanguage) {
            $this->throwLanguageIsocodeAlreadyExistsException($command->getIsocode());
        }

        $language = new Language(
            $this->languageRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName()),
            new AptoLocale($command->getIsocode())
        );

        $this->languageRepository->add($language);
        $language->publishEvents();
    }

    /**
     * @param UpdateLanguage $command
     */
    public function handleUpdateLanguage(UpdateLanguage $command)
    {
        $existingLanguage = $this->languageRepository->findOneByIsocode($command->getIsocode());
        $language = $this->languageRepository->findById($command->getId());

        if (null !== $language) {
            if (null !== $existingLanguage) {
                if ($existingLanguage->getId()->getId() != $language->getId()) {
                    $this->throwLanguageIsocodeAlreadyExistsException($command->getIsocode());
                }
            }

            $language
                ->setName($this->getTranslatedValue($command->getName()))
                ->setIsocode(new AptoLocale($command->getIsocode()));

            $this->languageRepository->update($language);
            $language->publishEvents();
        }
    }

    /**
     * @param RemoveLanguage $command
     * @throws LanguageNotDeletableException
     */
    public function handleRemoveLanguage(RemoveLanguage $command)
    {
        $count = $this->languageRepository->countLanguages();

        if ($count <= 1) {
            throw new LanguageNotDeletableException('Can not delete all Languages.');
        }

        $language = $this->languageRepository->findById($command->getId());

        if (null !== $language) {
            $this->languageRepository->remove($language);
            DomainEventPublisher::instance()->publish(
                new LanguageRemoved(
                    $language->getId()
                )
            );
        }
    }

    /**
     * @param string $isocode
     * @throws LanguageIsocodeAlreadyExistsException
     */
    protected function throwLanguageIsocodeAlreadyExistsException(string $isocode)
    {
        throw new LanguageIsocodeAlreadyExistsException('An language with an isocode \'' . $isocode . '\' already exists.');
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddLanguage::class => [
            'method' => 'handleAddLanguage',
            'bus' => 'command_bus'
        ];

        yield UpdateLanguage::class => [
            'method' => 'handleUpdateLanguage',
            'bus' => 'command_bus'
        ];

        yield RemoveLanguage::class => [
            'method' => 'handleRemoveLanguage',
            'bus' => 'command_bus'
        ];
    }
}