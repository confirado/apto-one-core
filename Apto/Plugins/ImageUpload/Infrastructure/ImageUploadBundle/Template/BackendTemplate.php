<?php

namespace Apto\Plugins\ImageUpload\Infrastructure\ImageUploadBundle\Template;

use Apto\Base\Infrastructure\AptoBaseBundle\Template\BackendTemplateInterface;

class BackendTemplate implements BackendTemplateInterface
{
    /**
     * @return array
     */
    public function getTemplates(): array
    {
        $data = [
            'angularTemplates' => [
                '@ImageUpload/image-upload/pages/canvas.html.twig'
            ]
        ];
        return [
            $data, 'backend'
        ];
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        $data = [
            'angular' => [
                '/plugin/image-upload/canvas/' => [
                    'templateUrl' => '@ImageUpload/image-upload/pages/canvas.html.twig',
                    'controller' => 'ImageUploadCanvasController'
                ]
            ]
        ];
        return [
            $data, 'backend'
        ];
    }

    /**
     * @return array
     */
    public function getMainMenuEntries(): array
    {
        $data = [
            '/plugin/image-upload/' => [
                'label' => 'Merchandise',
                'icon' => 'f279',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => ['ImageUploadFindCanvasList'],
                    'strategy' => 'one'
                ]),
                'subItems' => [
                    [
                        'route' => '/plugin/image-upload/canvas/',
                        'label' => 'Druckbereiche',
                        'icon' => 'f03e',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['ImageUploadFindCanvasList'],
                            'strategy' => 'all'
                        ])
                    ]
                ]
            ]
        ];
        return [
            $data, 'backend'
        ];
    }
}

