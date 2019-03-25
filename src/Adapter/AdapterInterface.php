<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter;

/**
 * Interface AdapterInterface
 *
 * @package Secretary\Adapter
 */
interface AdapterInterface
{
	/**
	 * @param array $options
	 *
	 * @return SecretInterface
	 */
	public function getSecret(array $options): SecretInterface;

	/**
	 * @param array $options
	 *
	 * @return SecretInterface[]
	 */
	public function getSecrets(array $options): array;

	/**
	 * @param array $options
	 *
	 * @return void
	 */
	public function putSecret(array $options): void;

	/**
	 * @param array $options
	 *
	 * @return void
	 */
	public function putSecrets(array $options): void;
}
