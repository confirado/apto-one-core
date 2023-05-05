<?php

namespace Apto\Base\Domain\Backend\Model\Acl;

class AclMask
{
    /**
     * several attributes, must be a power of 2
     */
    const NONE = 0;
    const CREATE = 1;
    const READ = 2;
    const UPDATE = 4;
    const DELETE = 8;
    const EXECUTE = 16;

    /**
     * @var int
     */
    protected $mask = 0;

    /**
     * array of all possible items
     * @var array
     */
    protected static $validAttributes = array(
        self::NONE,
        self::CREATE,
        self::READ,
        self::UPDATE,
        self::DELETE,
        self::EXECUTE
    );

    /**
     * value of the highest possible bitmask, can be calculated as sum over all items
     * @var int
     */
    protected static $maxValidAttributes = self::EXECUTE * 2 - 1;

    /**
     * AclMask constructor.
     * @param int $attributes
     * @throws AclMaskInvalidAttributeException
     */
    public function __construct(int $attributes = 0)
    {
        if ($attributes > self::$maxValidAttributes) {
            throw new AclMaskInvalidAttributeException('The given attributes \'' . $attributes . '\' are invalid.');
        }
        $this->mask = $attributes;
    }

    /**
     * check, whether the given attribute/s is/are set in this mask
     * @param int $attribute
     * @return bool
     * @throws AclMaskInvalidAttributeException
     */
    public function hasAttribute(int $attribute): bool
    {
        if (!in_array($attribute, self::$validAttributes)) {
            throw new AclMaskInvalidAttributeException('The given attribute \'' . $attribute . '\' is invalid.');
        }

        return ($this->mask & $attribute) == $attribute;
    }

    /**
     * check, whether this mask is included in the given mask's attributes (is equal or less powerful)
     * @param AclMask $mask
     * @return bool
     * @internal param int $attributes
     */
    public function matches(AclMask $mask): bool
    {
        $attributes = $mask->getAttributes();
        return ($attributes & $this->mask) == $this->mask;
    }

    /**
     * check, whether this mask includes the given mask's attributes (is at least as powerful)
     * @param AclMask $mask
     * @return bool
     * @internal param int $attributes
     */
    public function matchedBy(AclMask $mask): bool
    {
        $attributes = $mask->getAttributes();
        return ($this->mask & $attributes) == $attributes;
    }

    /**
     * @return int
     */
    public function getAttributes(): int
    {
        return $this->mask;
    }

    public function isNone(): bool
    {
        return $this->mask == self::NONE;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->mask;
    }

    /**
     * @return string
     */
    public function getHumanReadable(): string
    {
        return
            (($this->mask & self::CREATE ) == self::CREATE  ? 'C' : '.') .
            (($this->mask & self::READ   ) == self::READ    ? 'R' : '.') .
            (($this->mask & self::UPDATE ) == self::UPDATE  ? 'U' : '.') .
            (($this->mask & self::DELETE ) == self::DELETE  ? 'D' : '.') .
            (($this->mask & self::EXECUTE) == self::EXECUTE ? 'E' : '.');
    }
}