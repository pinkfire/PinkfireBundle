<?php

namespace Pinkfire\PinkfireBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pinkfire');

        $rootNode
            ->canBeDisabled()
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
                ->integerNode('log_max_length')
                    ->defaultValue(-1)
                ->end()
                ->arrayNode('url_blacklist')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('url_debug')
                    ->addDefaultChildrenIfNoneSet()
                    ->prototype('scalar')->defaultValue('_.*')->end()
                ->end()
                ->scalarNode('log_level')
                    ->defaultValue('300')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
