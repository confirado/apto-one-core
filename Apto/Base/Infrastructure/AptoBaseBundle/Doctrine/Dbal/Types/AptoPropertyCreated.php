<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Dbal\Types;

use DateTimeImmutable;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Exception\Types\AptoPropertyCreatedException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class AptoPropertyCreated extends Type
{
    const APTO_PROPERTY_CREATED = 'AptoPropertyCreated';

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'DATETIME(6)';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::APTO_PROPERTY_CREATED;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string
     * @throws AptoPropertyCreatedException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if($value instanceof DateTimeImmutable) {
            return $value->format('Y-m-d H:i:s.u');
        }

        throw new AptoPropertyCreatedException('Value of Type \'AptoPropertyCreated\' must be an instance of \'DateTimeImmutable\'');
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return DateTimeImmutable|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTimeImmutable
    {
        if (null === $value || false === $value) {
            return null;
        }

        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
