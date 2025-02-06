<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 19.04.2016
 * Time: 08:25.
 */

namespace Turted\TurtedBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Turted\TurtedBundle\Service\TurtedPushService;

class TurtedExtension extends ConfigurableExtension
{
    /**
     * @return void
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('service.yml');

        $definition = $container->getDefinition(TurtedPushService::class);
        $definition->setArgument(0, $mergedConfig);
    }
}
