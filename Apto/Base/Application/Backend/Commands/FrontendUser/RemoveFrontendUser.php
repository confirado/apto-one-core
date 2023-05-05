<?php

namespace Apto\Base\Application\Backend\Commands\FrontendUser;

use Apto\Base\Application\Core\CommandInterface;

class RemoveFrontendUser implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * RemoveUserRole constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
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
