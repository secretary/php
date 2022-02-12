<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Exception;

class ValueNotSupportedException extends \Exception
{
    public function __construct(string $key)
    {
        parent::__construct('This adapter doesn\'t support storing the value passed for: "'.$key.'"');
    }
}
