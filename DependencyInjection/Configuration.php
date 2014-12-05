<?php

namespace Gloomy\SosSoaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gloomy_sos_soa');

        $rootNode
            ->children()
                ->scalarNode('application')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('host')
                    ->defaultValue('localhost')
                ->end()
                ->integerNode('port')
                    ->defaultValue(3000)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
