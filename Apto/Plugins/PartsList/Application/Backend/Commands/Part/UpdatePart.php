<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

class UpdatePart extends PartAbstract
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     * @param bool $active
     * @param string $partNumber
     * @param string|null $unitId
     * @param array $name
     * @param array $description
     * @param int|null $amount
     * @param string|null $currencyCode
     * @param string|null $category
     * @param array $customProperties
     */
    public function __construct(string $id, bool $active, string $partNumber, ?string $unitId, array $name, array $description, ?int $amount, ?string $currencyCode, ?string $category, array $customProperties)
    {
        parent::__construct($active, $partNumber, $unitId, $name, $description, $amount, $currencyCode, $category, $customProperties);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
