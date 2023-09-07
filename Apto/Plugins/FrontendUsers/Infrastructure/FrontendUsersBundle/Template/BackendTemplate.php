<?php

namespace Apto\Plugins\FrontendUsers\Infrastructure\FrontendUsersBundle\Template;

use Apto\Base\Infrastructure\AptoBaseBundle\Template\BackendTemplateInterface;

class BackendTemplate implements BackendTemplateInterface
{
    public function getMainMenuEntries(): array
    {
        $data = [
            '/user-login/' => [
                'route' => '/frontend-users/',
                'label' => 'Frontend Benutzer',
                'icon' => 'f0c0',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => [],
                    'strategy' => 'all'
                ]),
            ]
        ];
        return [
            $data, 'backend'
        ];
    }

    public function getRoutes(): array
    {
        $data = [
            'angular' => [
                '/frontend-users/' => [
                    'templateUrl' => '@FrontendUsers/backend/pages/frontend-users.html.twig',
                    'controller' => 'FrontendUserController'
                ],
            ]
        ];
        return [
            $data, 'backend'
        ];
    }

    public function getTemplates(): array
    {
        $data = [
            'angularTemplates' => [
                '@FrontendUsers/backend/pages/frontend-users.html.twig'
            ]
        ];
        return [
            $data, 'backend'
        ];
    }
}