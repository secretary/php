<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Exception;


class ValueNotSupportedException extends \Exception
{
	/**
	 * @param string $key
	 */
	public function __construct(string $key)
	{
		parent::__construct('This adapter doesn\'t support storing the value passed for: "'.$key.'"');
	}
}
