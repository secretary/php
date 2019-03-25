<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Helper;


abstract class ArrayHelper
{
	/**
	 * @param array    $array
	 * @param string[] ...$keys
	 *
	 * @return array
	 */
	public static function without(array $array, ...$keys)
	{
		$newArray = $array;
		foreach ($keys as $key) {
			unset($newArray[$key]);
		}

		return $newArray;
	}
}
