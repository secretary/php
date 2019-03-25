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
 * Class GetSecretsOptionsConfiguration
 *
 * @package Secretary\Configuration
 */
class GetSecretsOptionsConfiguration extends AbstractOptionsConfiguration
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
                ->scalarNode('path')->isRequired()->end()
            ->end();

        $this->validatePath($rootNode);

        return $rootNode;
	}
}
