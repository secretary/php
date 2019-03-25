<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter;

/**
 * Interface SecretWithPath
 */
class SecretWithPath extends Secret
{
    /**
     * @var string
     */
    private $path;

    /**
     * SecretWithPath constructor.
     *
     * @param string $key
     * @param string $value
     * @param string $path
     */
    public function __construct(string $key, string $value, string $path)
    {
        parent::__construct($key, $value);
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
