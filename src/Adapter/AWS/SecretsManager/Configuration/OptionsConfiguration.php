<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Adapter\AWS\Configuration;


use Secretary\Configuration\Adapter\AbstractAdapterConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class OptionsConfiguration extends AbstractAdapterConfiguration
{
	protected function getRootNode(ArrayNodeDefinition $rootNode): ArrayNodeDefinition
	{
		return $rootNode->ignoreExtraKeys(false);
	}
}
