<?php

namespace Apto\Base\Domain\Core\Model;

use Ramsey\Uuid\Uuid;

class AptoUuid implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @param string|null $id
     * @return self|null
     * @throws InvalidUuidException
     */
    public static function fromId(?string $id): ?self
    {
        return null === $id ? null : new self($id);
    }

    /**
     * AptoUuid constructor.
     * @param null|string $id
     * @throws InvalidUuidException
     */
    public function __construct($id = null)
    {
        if (null === $id) {
            $id = Uuid::uuid4()->toString();
        } elseif (!Uuid::isValid($id)) {
            throw new InvalidUuidException('Given id is not a valid uuid.');
        }

        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->id;
    }

}
