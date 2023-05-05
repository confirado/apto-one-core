<?php

namespace Apto\Base\Application\Core\Service\Translation;

use Apto\Base\Application\Core\Service\Translation\Exceptions\TranslationTypeNotFoundException;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;

abstract class AbstractTranslationExportProvider implements TranslationExportProvider
{
    const TRANSLATION_TYPE = 'SET_TRANSLATION_TYPE_IN_CHILD_CLASS';

    /**
     * @var string
     */
    private $translationType;

    /**
     * @throws TranslationTypeNotFoundException
     */
    public function __construct()
    {
        $this->translationType = static::TRANSLATION_TYPE;

        if ($this->translationType === self::TRANSLATION_TYPE) {
            throw new TranslationTypeNotFoundException('TranslationType not set in child class.');
        }
    }

    /**
     * @param string $fieldName
     * @param array $translatedValue
     * @param string $itemId
     * @return TranslationItem
     * @throws InvalidUuidException
     */
    protected function makeTranslationItem(string $fieldName, array $translatedValue, string $itemId): TranslationItem
    {
        return new TranslationItem(
            $this->translationType,
            $fieldName,
            AptoTranslatedValue::fromArray($translatedValue),
            new AptoUuid($itemId)
        );
    }
}