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
        $treeBuilder = new TreeBuilder('howmas_corems');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('seo')
                    ->children()
                        ->scalarNode('image_thumbnail')
                            ->info('SEO image thumbnail')
                            ->defaultValue(null)
                        ->end()
                        ->booleanNode('autofill_meta_tags')
                            ->info('Automatically fill data to meta tags (og/twitter) if config null')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
