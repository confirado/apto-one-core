<?php
namespace Apto\Base\Infrastructure\AptoBaseBundle\Security\FrontendUser;

interface AptoTokenFrontendUserInterface
{
    /**
     * @return string
     */
    public function getId();
}
