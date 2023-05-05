<?php
namespace Apto\Base\Application\Core\Service\Translation;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;

class TranslationItem
{
    /**
     * @var AptoTranslatedValue
     */
    private $translatedValue;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $translationType;

    /**
     * @var AptoUuid
     */
    private $entityId;

    /**
     * TranslationItem constructor.
     * @param string $translationType
     * @param string $fieldName
     * @param AptoTranslatedValue $translatedValue
     * @param AptoUuid $entityId
     */
    public function __construct(string $translationType, string $fieldName, AptoTranslatedValue $translatedValue, AptoUuid $entityId)
    {
        $this->translationType = $translationType;
        $this->fieldName = $fieldName;
        $this->translatedValue = $translatedValue;
        $this->entityId = $entityId;
    }

    /**
     * @return AptoTranslatedValue
     */
    public function getTranslatedValue(): AptoTranslatedValue
    {
        return $this->translatedValue;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @return AptoUuid
     */
    public function getEntityId(): AptoUuid
    {
        return $this->entityId;
    }

    /**
     * @return string
     */
    public function getTranslationType(): string
    {
        return $this->translationType;
    }
}
