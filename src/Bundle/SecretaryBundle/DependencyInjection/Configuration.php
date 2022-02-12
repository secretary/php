<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Bundle\SecretaryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @package Secretary\Bundle\SecretaryBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('secretary');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->append($this->addAdaptersSection())
            ->end();

        return $treeBuilder;
    }

    private function addAdaptersSection(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('adapters');
        $node        = $treeBuilder->getRootNode();

        $node
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('adapter')
            ->isRequired()
            ->info('Class name, or service ID of adapter')
            ->end()
            ->arrayNode('config')->ignoreExtraKeys(false)->end()
            ->arrayNode('cache')
            ->canBeEnabled()
            ->addDefaultsIfNotSet()
            ->children()
            ->enumNode('type')->values(['psr6', 'psr16'])->end()
            ->scalarNode('service_id')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $node;
    }
}
