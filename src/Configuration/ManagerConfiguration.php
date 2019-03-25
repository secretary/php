<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Configuration;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Secretary\Adapter\AdapterInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ManagerConfiguration implements ConfigurationInterface
{
	/**
	 * Generates the configuration tree builder.
	 *
	 * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
	 */
	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('manager');

		/** @var ArrayNodeDefinition|NodeDefinition $rootNode */
		$rootNode = $treeBuilder->getRootNode();

		$rootNode->children()
			->append($this->addCacheNode())
			->end();

		$this->addAdapterNode($rootNode);

		return $treeBuilder;
	}
	/**
	 * Normalizes the enabled field to be truthy.
	 *
	 * @param NodeDefinition $node
	 *
	 * @return ManagerConfiguration
	 */
	private function normalizeEnabled(NodeDefinition $node): self
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

	private function addAdapterNode(ArrayNodeDefinition $rootNode): void
	{
		$rootNode
			->children()
				->variableNode('adapter')
					->isRequired()
					->validate()
						->ifTrue(
							function ($v) {
								return !$v instanceof AdapterInterface;
							}
						)
						->thenInvalid('`adapter` must be an instance of ' . AdapterInterface::class)
					->end()
				->end()
			->end();
	}

	private function addCacheNode(): ArrayNodeDefinition
	{
		$treeBuilder = new TreeBuilder('cache');

		/** @var ArrayNodeDefinition|NodeDefinition $rootNode */
		$rootNode = $treeBuilder->getRootNode();

		$rootNode
			->canBeEnabled()
			->addDefaultsIfNotSet()
			->children()
				->variableNode('instance')
					->validate()
						->ifTrue(
							function ($v) {
								return !($v instanceof CacheInterface || $v instanceof CacheItemPoolInterface);
							}
						)
						->thenInvalid('`instance` must be an instance of ' . CacheInterface::class . ' or ' . CacheItemPoolInterface::class )
					->end()
					->validate()
						->ifTrue(
							function ($v, $options) {
								return $options['enabled'] && empty($v);
							}
						)
						->thenInvalid('`instance` must be a set if cache is enabled')
					->end()
				->end()
			->end();

		$this->normalizeEnabled($rootNode);

		return $rootNode;
	}
}
