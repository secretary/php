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
		return $rootNode;
	}
}
