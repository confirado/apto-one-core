<?php

namespace Apto\Catalog\Application\Core\Service\Translation;

use Apto\Catalog\Domain\Core\Model\Group\GroupRepository;
use Apto\Base\Application\Core\Service\Translation\TranslationImportProvider;
use Apto\Base\Application\Core\Service\Translation\TranslationItem;
use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslatedTypeNotMatchingException;

class GroupTranslationImportProvider implements TranslationImportProvider
{
    const TRANSLATION_TYPE = 'Group';

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var string
     */
    private $translationType;

    /**
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->translationType = self::TRANSLATION_TYPE;
    }

    /**
     * @param TranslationItem $translationItem
     * @throws TranslatedTypeNotMatchingException
     */
    public function setTranslatedValue(TranslationItem $translationItem): void
    {
        if ($translationItem->getTranslationType() !== $this->translationType) {
            throw new TranslatedTypeNotMatchingException($translationItem->getTranslationType(), $this->translationType);
        }

        $groupEntity = $this->groupRepository->findById($translationItem->getEntityId()->getId());
        if (null === $groupEntity) {
            return;
        }

        $fieldNameArray = explode('_', $translationItem->getFieldName());
        if ($fieldNameArray[1] === 'name' ) {
            $groupEntity->setName($translationItem->getTranslatedValue());
        }

        $this->groupRepository->update($groupEntity);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->translationType;
    }
}