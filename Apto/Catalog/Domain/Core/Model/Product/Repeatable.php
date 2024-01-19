<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

class Repeatable implements \JsonSerializable
{
    const TYPES = ['Statisch', 'Wiederholbar'];

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string|null
     */
    private ?string $calculatedValueName;

    /**
     * @param array $repeatable
     *
     * @return self
     * @throws RepeatableValidationException
     */
    public static function fromArray(array $repeatable): self
    {
        if (array_key_exists('type', $repeatable) && array_key_exists('calculatedValueName', $repeatable)) {
            return new self($repeatable['type'], $repeatable['calculatedValueName']);
        }

        throw new RepeatableValidationException('Argument "$repeatable" must have set both "type" and "calculatedValueName" keys');
    }

    /**
     * @param string      $type
     * @param string|null $calculatedValueName
     *
     * @throws RepeatableValidationException
     */
    public function __construct(string $type, ?string $calculatedValueName = null)
    {
        $this->assertValidValues($type, $calculatedValueName);

        $this->type = $type;
        $this->calculatedValueName = $calculatedValueName;
    }

    /**
     * @return bool
     */
    public function isRepeatable(): bool
    {
        return $this->type === self::TYPES[1];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return ['type' => $this->type, 'calculatedValueName' => $this->calculatedValueName];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getCalculatedValueName(): ?string
    {
        return $this->calculatedValueName;
    }

    /**
     * @param Repeatable $repeatable
     *
     * @return bool
     */
    public function equals(Repeatable $repeatable): bool
    {
        return $this->getType() === $repeatable->getType() && $this->getCalculatedValueName() === $repeatable->getCalculatedValueName();
    }

    /**
     * @param string      $type
     * @param string|null $calculatedValueName
     *
     * @return void
     * @throws RepeatableValidationException
     */
    private function assertValidValues(string $type, ?string $calculatedValueName): void
    {
        if (!in_array($type, self::TYPES)) {
            throw new RepeatableValidationException('Invalid argument "$type": ' . $type);
        }

        if ($type === self::TYPES[1] && ($calculatedValueName === null || trim($calculatedValueName) === '')) {
            throw new RepeatableValidationException('When Argument "$type" is set to "Wiederholbar" argument "$calculatedValueName" can not be empty');
        }
    }
}
