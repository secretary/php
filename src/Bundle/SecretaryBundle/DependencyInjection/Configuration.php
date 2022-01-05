<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Bundle\SecretaryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Secretary\Bundle\SecretaryBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('secretary');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('secretary');
        }

        $rootNode
            ->children()
                ->append($this->addAdaptersSection())
            ->end();

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function addAdaptersSection(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('adapters');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $node = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $node = $treeBuilder->root('adapters');
        }

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
