<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Configuration\Adapter;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class AbstractAdapterConfiguration implements ConfigurationInterface
{
	/**
	 * Generates the configuration tree builder.
	 *
	 * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
	 */
	public final function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('adapter');

		/** @var ArrayNodeDefinition|NodeDefinition $rootNode */
		$rootNode = $treeBuilder->getRootNode();

		$this->getRootNode($rootNode);

		return $treeBuilder;
	}
	/**
	 * Normalizes the enabled field to be truthy.
	 *
	 * @param NodeDefinition $node
	 *
	 * @return ManagerConfiguration
	 */
	protected function normalizeEnabled(NodeDefinition $node): self
	{
		$node->beforeNormalization()
			->always()
			->then(
				function ($v) {
					if (is_string($v['enabled'])) {
						$v['enabled'] = $v['enabled'] === 'true';
					}
					if (is_int($v['enabled'])) {
						$v['enabled'] = $v['enabled'] === 1;
					}
					return $v;
				}
			)
			->end();

		return $this;
	}

	protected abstract function getRootNode(ArrayNodeDefinition $rootNode): ArrayNodeDefinition;
}
