<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Helper;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

abstract class OptionsResolverHelper
{
	/**
	 * @param array                  $options
	 * @param ConfigurationInterface $configuration
	 * @param string                 $key
	 *
	 * @return array
	 */
	public static function process(array $options): array {
		$processor = new Processor();

		return $processor->processConfiguration($configuration, [$key => $options]);
	}
}
