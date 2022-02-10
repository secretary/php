<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

namespace Secretary;

use Secretary\Exception\ValueNotSupportedException;

/**
 * @package Secretary
 */
class Secret implements \ArrayAccess
{
    private string $key;

    /**
     * @var string|array
     */
    private $value;

    private ?array $metadata = null;

    /**
     * @param string       $key
     * @param string|array $value
     * @param array|null   $metadata
     */
    public function __construct(string $key, $value, ?array $metadata = null)
    {
        $this->key      = $key;
        $this->value    = $value;
        $this->metadata = $metadata;
    }

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

    public function getMetadata(): array
    {
        return $this->metadata ?? [];
    }

    public function offsetExists(mixed $offset): bool
    {
        return is_array($this->value) && array_key_exists($offset, $this->value);
    }

    /**
     * @throws \Exception
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (!is_array($this->value)) {
            throw new ValueNotSupportedException($this->key);
        }

        return $this->value[$offset];
    }

    /**
     * @throws \Exception
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \Exception('Secrets are immutable');
    }

    /**
     * @throws \Exception
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \Exception('Secrets are immutable');
    }

    /**
     * Returns a new instance of this secret with the value changed
     *
     * @param string|array $value
     *
     * @return Secret
     */
    public function withValue($value): Secret
    {
        return new Secret($this->key, $value, $this->metadata);
    }

    /**
     * Returns a new instance of this secret with the metadata changed
     *
     * @param array $metadata
     *
     * @return Secret
     */
    public function withMetadata(array $metadata): Secret
    {
        return new Secret($this->key, $this->value, $metadata);
    }
}
