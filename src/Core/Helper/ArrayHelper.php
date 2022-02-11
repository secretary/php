<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Helper;

/**
 * Class ArrayHelper.
 *
 * @package Secretary\Helper
 */
abstract class ArrayHelper
{
    /**
     * @param string ...$keys
     */
    public static function without(array $array, ...$keys): array
    {
        $newArray = $array;
        foreach ($keys as $key) {
            if (!empty($newArray[$key])) {
                unset($newArray[$key]);
            }
        }

        return $newArray;
    }

    /**
     * @param string ...$keys
     */
    public static function remove(array & $array, ...$keys): array
    {
        $newArray = [];
        foreach (array_keys($newArray) as $key) {
            if (!in_array($key, $keys)) {
                $newArray[$key] = $array[$key];
                unset($array[$key]);
            }
        }

        foreach ($keys as $key) {
            if (!array_key_exists($key, $newArray)) {
                $newArray[$key] = null;
            }
        }

        return $newArray;
    }
}
