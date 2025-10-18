<?php

namespace Evoris\Core\DependecyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @psalm-type Config = array{
 *      pages: list<string>
 * }
 */
final class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('evoris_core');

        // @codingStandardsIgnoreStart
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()
            ->arrayNode('pages')
                ->beforeNormalization()->castToArray()->end()
                ->defaultValue([])
                ->scalarPrototype()->end()
            ->end()
        ->end();
        // @codingStandardsIgnoreEnd

        return $treeBuilder;
    }
}