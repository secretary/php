<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Exception;

class SecretNotFoundException extends \Exception
{
    public function __construct(string $key, ?\Exception $childException = null)
    {
        parent::__construct('No secret was found with the key: "'.$key.'"', 404, $childException);
    }
}
