<?php

namespace JoliTypo\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('joli_typo');

        $rootNode
            ->fixXmlConfig('preset')
            ->children()
                ->arrayNode('presets')
                    ->useAttributeAsKey('name')
                    ->isRequired()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('locale')->end()
                            ->arrayNode('fixers')
                                ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
