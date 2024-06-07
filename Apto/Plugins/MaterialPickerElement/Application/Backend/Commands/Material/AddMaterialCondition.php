<?php
namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class AddMaterialCondition implements CommandInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $conditionId;

    /**
     * @param string $id
     * @param string $conditionId
     */
    public function __construct(string $id, string $conditionId)
    {
        $this->id = $id;
        $this->conditionId = $conditionId;
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
    public function getConditionId(): string
    {
        return $this->conditionId;
    }
}
