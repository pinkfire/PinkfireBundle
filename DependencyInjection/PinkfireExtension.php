<?php

namespace Pinkfire\PinkfireBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class PinkfireExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('pinkfire.application', $config['application']);
        $container->setParameter('pinkfire.host', $config['host']);
        $container->setParameter('pinkfire.port', $config['port']);
        $container->setParameter('pinkfire.url_blacklist', $config['url_blacklist']);
        $container->setParameter('pinkfire.url_debug', $config['url_debug']);
        $container->setParameter('pinkfire.log_max_length', $config['log_max_length']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
