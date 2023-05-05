<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Dbal\Types;

use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoTranslatedValueItem;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Exception\Types\AptoPropertyTranslatedValueException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;

class AptoPropertyTranslatedValue extends TextType
{
    const APTO_PROPERTY_TRANSLATED_VALUE = 'AptoPropertyTranslatedValue';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::APTO_PROPERTY_TRANSLATED_VALUE;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string|null
     * @throws AptoPropertyTranslatedValueException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof AptoTranslatedValue) {
            return $value->__toString();
        }

        throw new AptoPropertyTranslatedValueException('Value of Type \'AptoPropertyTranslatedValue\' must be an instance of \'AptoTranslatedValue\'');
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return AptoTranslatedValue
     * @throws AptoPropertyTranslatedValueException
     * @throws InvalidTranslatedValueException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): AptoTranslatedValue
    {
        try {
            $value = json_decode($value, JSON_OBJECT_AS_ARRAY);
        }
        catch (\Exception $e) {
            throw new AptoPropertyTranslatedValueException('Cannot convert database value to Type \'AptoTranslatedValue\' due to malformed JSON data');
        }

        //@todo how to handle null/empty values?
        if (null === $value || '' === $value) {
            return new AptoTranslatedValue([]);
        }

        $translations = array();
        foreach ($value as $isoname => $translation) {
            $isocode = new AptoLocale($isoname);
            $translations[$isocode->getName()] = new AptoTranslatedValueItem($isocode, $translation);
        }

        return new AptoTranslatedValue($translations);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
