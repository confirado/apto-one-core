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
     */
    public function __construct(string $id, bool $active, string $partNumber, ?string $unitId, array $name, array $description, ?int $amount, ?string $currencyCode)
    {
        parent::__construct($active, $partNumber, $unitId, $name, $description, $amount, $currencyCode);
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
