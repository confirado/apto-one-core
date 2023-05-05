<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Dbal\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;

use Apto\Base\Domain\Core\Model\AptoJsonSerializable;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;

class AptoPropertyJsonSerializable extends TextType
{
    const APTO_PROPERTY_ELEMENT_DEFINITION = 'AptoPropertyJsonSerializable';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::APTO_PROPERTY_ELEMENT_DEFINITION;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        $aptoJsonSerializer = new AptoJsonSerializer();
        $serialized = $aptoJsonSerializer->jsonSerialize($value);

        return $serialized === false ? null : $serialized;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return AptoJsonSerializable
     * @throws AptoJsonSerializerException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): AptoJsonSerializable
    {
        $aptoJsonSerializer = new AptoJsonSerializer();
        return $aptoJsonSerializer->jsonUnSerialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}