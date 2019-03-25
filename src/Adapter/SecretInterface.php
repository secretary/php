<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter;

/**
 * Interface SecretInterface
 *
 * @package Secretary\Adapter
 */
interface SecretInterface {
	/**
	 * @return string
	 */
	public function getKey(): string;

	/**
	 * @return string
	 */
	public function getValue(): string;
}
