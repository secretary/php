<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\AWS\SecretsManager\Configuration;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class GetSecretsOptionsConfiguration
 *
 * @package Secretary\Adapter\AWS\SecretsManager\Configuration
 */
class GetSecretsOptionsConfiguration extends \Secretary\Configuration\Adapter\Path\GetSecretsOptionsConfiguration
{
	/**
	 * @param ArrayNodeDefinition $rootNode
	 *
	 * @return ArrayNodeDefinition
	 */
	protected function getRootNode(ArrayNodeDefinition $rootNode): ArrayNodeDefinition
	{
		return parent::getRootNode($rootNode)
			->children()
				->scalarNode('versionId')->defaultNull()->end()
				->scalarNode('versionStage')->defaultNull()->end()
			->end();

	}
}
