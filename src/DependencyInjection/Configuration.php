<?php

namespace HowMAS\CoreMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('how_mas_core_ms');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('document')
                    ->children()
                        ->arrayNode('listing')
                            ->children()
                                ->booleanNode('showRoots')
                                    ->info('Show or hide document roots - document with key is language code')
                                    ->defaultTrue()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('object')
                    ->children()
                        ->arrayNode('listing')
                            ->children()
                                ->arrayNode('condition')
                                    ->arrayPrototype()
                                        ->children()
                                            ->scalarNode('query_string')
                                                ->info('Change or add more condition while listing data')
                                            ->end()
                                            ->arrayNode('condition_array')
                                                ->useAttributeAsKey('name')
                                                ->prototype('scalar')
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
