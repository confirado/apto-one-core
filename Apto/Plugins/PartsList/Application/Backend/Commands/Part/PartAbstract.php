<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

use Apto\Base\Application\Core\CommandInterface;

abstract class PartAbstract implements CommandInterface
{
    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $partNumber;

    /**
     * @var string|null
     */
    private $unitId;

    /**
     * @var array
     */
    private $name;

    /**
     * @var array
     */
    private $description;

    /**
     * @var int|null
     */
    private $amount;

    /**
     * @var string|null
     */
    private $currencyCode;

    /**
     * @var string|null
     */
    private ?string $category;

    /**
     * @param bool $active
     * @param string $partNumber
     * @param string|null $unitId
     * @param array $name
     * @param array $description
     * @param int|null $amount
     * @param string|null $currencyCode
     */
    public function __construct(bool $active, string $partNumber, ?string $unitId, array $name, array $description, ?int $amount, ?string $currencyCode, ?string $category)
    {
        $this->active = $active;
        $this->partNumber = $partNumber;
        $this->unitId = $unitId;
        $this->name = $name;
        $this->description = $description;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
        $this->category = $category;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getPartNumber(): string
    {
        return $this->partNumber;
    }

    /**
     * @return string|null
     */
    public function getUnitId(): ?string
    {
        return $this->unitId;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getDescription(): array
    {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @return string|null
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * @return string|null
     */
    public function getCategoryId(): ?string
    {
        return $this->category;
    }
}
