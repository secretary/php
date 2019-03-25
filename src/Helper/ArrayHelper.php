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
	/**
	 * @param array    $array
	 * @param string[] ...$keys
	 *
	 * @return array
	 */
	public static function with(array $array, ...$keys)
	{
		$newArray = $array;
		foreach (array_keys($newArray) as $key ) {
			if (!in_array($key, $keys)) {
				unset($newArray[$key]);
			}
		}

		return $newArray;
	}
}
