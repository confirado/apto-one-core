<?php

namespace Apto\Plugins\HintElement\Infrastructure\HintElementBundle;

use Apto\Base\Infrastructure\AptoBaseBundle\AbstractAptoBundle;
use Apto\Plugins\HintElement\Infrastructure\HintElementBundle\DependencyInjection\HintElementExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class HintElementBundle extends AbstractAptoBundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);
	}

	protected function getAlias()
    {
        return 'plugin_hint_element_core_bundle';
    }

	/**
	 * @return Extension
	 */
    public function getContainerExtension()
    {
        return new HintElementExtension();
    }
}