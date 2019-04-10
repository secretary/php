<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Exception;


class SecretNotFoundException extends \Exception
{
	/**
	 * @param string $key
	 */
	public function __construct(string $key)
	{
		parent::__construct('No secret was found with the key: "'.$key.'"');
	}
}
