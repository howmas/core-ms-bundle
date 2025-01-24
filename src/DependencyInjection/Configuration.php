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
                ->arrayNode('system')
                    ->children()
                        ->arrayNode('admin')
                            ->children()
                                ->arrayNode('access_core_names')
                                    ->info('List of admin name can access core UI')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('template')
                    ->children()
                        ->arrayNode('form')
                            ->children()
                                ->arrayNode('numeric')
                                    ->children()
                                        ->arrayNode('js_masked')
                                            ->children()
                                                ->booleanNode('enable')
                                                    ->defaultTrue()
                                                ->end()
                                                ->scalarNode('template')
                                                    ->info('Custom template')
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ecommerce')
                    ->children()
                        ->booleanNode('enable')
                            ->info('Include ecommerce layout')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('classes')
                            ->children()
                                ->scalarNode('category')
                                    ->info('Name of Category class')
                                ->end()
                                ->arrayNode('products')
                                    ->info('List of name of Product classes')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->scalarNode('order')
                                    ->info('Name of Order class')
                                ->end()
                                ->arrayNode('others')
                                    ->info('List of name of other ecommerce classes')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
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
                                            ->scalarNode('orderKey')
                                            ->end()
                                            ->enumNode('order')
                                                ->values(['asc', 'desc', 'rand'])
                                            ->end()
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
