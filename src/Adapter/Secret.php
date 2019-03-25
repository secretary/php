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
class Secret
{
	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string
	 */
	private $value;

	/**
	 * SecretWithPath constructor.
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function __construct(string $key, string $value)
	{
		$this->key   = $key;
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * @return string
	 */
	public function getValue(): string
	{
		return $this->value;
	}
}
