<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Configuration\Adapter\Path;


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
        $rootNode
            ->children()
                ->scalarNode('key')->isRequired()->end()
                ->scalarNode('value')->isRequired()->end()
                ->scalarNode('path')->isRequired()->end()
            ->end();

        $this->validatePath($rootNode);

        return $rootNode;
    }
}
