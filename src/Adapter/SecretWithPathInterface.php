<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter;

/**
 * Interface SecretWithPathInterface
 */
interface SecretWithPathInterface extends SecretInterface
{
    /**
     * @return string
     */
    public function getPath(): string;
}
