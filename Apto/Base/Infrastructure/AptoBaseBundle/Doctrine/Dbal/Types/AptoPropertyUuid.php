<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Dbal\Types;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Exception\Types\AptoPropertyUuidException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Ramsey\Uuid\Uuid;

class AptoPropertyUuid extends StringType
{
    const APTO_PROPERTY_UUID = 'AptoPropertyUuid';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::APTO_PROPERTY_UUID;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string|null
     * @throws AptoPropertyUuidException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof AptoUuid) {
            return $value->getId();
        }

        throw new AptoPropertyUuidException('Value of Type \'AptoPropertyUuid\' must be an instance of \'AptoUuid\' or null');
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return AptoUuid|null
     * @throws AptoPropertyUuidException
     * @throws InvalidUuidException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?AptoUuid
    {
        if (!$value) {
            return null;
        }

        if (Uuid::isValid($value)) {
            return new AptoUuid($value);
        }

        throw new AptoPropertyUuidException('Value of Type \'AptoPropertyUuid\' must be a valid uuid or null');
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}