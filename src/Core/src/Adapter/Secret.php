<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

namespace Secretary\Adapter;

/**
 * Interface SecretInterface
 *
 * @package Secretary\Adapter
 */
class Secret implements \ArrayAccess
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string|array
     */
    private $value;

    /**
     * SecretWithPath constructor.
     *
     * @param string       $key
     * @param string|array $value
     */
    public function __construct(string $key, $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return is_array($this->value) && array_key_exists($offset, $this->value);
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function offsetGet($offset)
    {
        if (!is_array($this->value)) {
            throw new \Exception('Secret is not an array');
        }

        return $this->value[$offset];
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception('Secrets are immutable');
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('Secrets are immutable');
    }
}
