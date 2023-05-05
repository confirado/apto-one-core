<?php

namespace Apto\Catalog\Application\Backend\Commands\Shop;

use Apto\Base\Application\Core\CommandInterface;

class UpdateShopOperator implements CommandInterface
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string|null
     */
    private ?string $mail;

    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @param string $id
     * @param string|null $mail
     * @param string|null $name
     */
    public function __construct(string $id, ?string $mail, ?string $name)
    {
        $this->id = $id;
        $this->mail = $mail;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
