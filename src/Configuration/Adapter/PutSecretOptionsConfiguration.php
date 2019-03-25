<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Configuration\Adapter;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class PutSecretOptionsConfiguration
 *
 * @package Secretary\Configuration
 */
class PutSecretOptionsConfiguration extends AbstractOptionsConfiguration
{
    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return ArrayNodeDefinition
     */
    protected function getRootNode(ArrayNodeDefinition $rootNode): ArrayNodeDefinition
    {
        return $rootNode
            ->children()
                ->scalarNode('key')->isRequired()->end()
                ->scalarNode('value')->isRequired()->end()
            ->end();
    }
}
