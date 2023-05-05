<?php
namespace Apto\Base\Infrastructure\AptoBaseBundle\Security\User;

interface AptoTokenUserInterface
{
    /**
     * @return string
     */
    public function getId();
}