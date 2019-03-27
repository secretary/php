<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */


namespace Secretary\Exception;

/**
 * Class InvalidPathException
 *
 * @package Secretary\Exception
 */
class InvalidPathException extends \Exception
{
    /**
     * InvalidPathException constructor.
     *
     * @param string $path
     * @param string $regex
     */
    public function __construct(string $path, string $regex)
    {
        parent::__construct(sprintf('%s is not a valid path. Must match regex: %s', $path, $regex), 400);
    }
}
