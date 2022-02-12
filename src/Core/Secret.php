<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
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
     * @var array|string
     */
    private $value;

    private ?array $metadata = null;

    /**
     * @param array|string $value
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
     * @return array|string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getMetadata(): array
    {
        return $this->metadata ?? [];
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return is_array($this->value) && array_key_exists($offset, $this->value);
    }

    /**
     * @param mixed $offset
     *
     * @throws ValueNotSupportedException
     */
    public function offsetGet($offset): mixed
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
     * Returns a new instance of this secret with the value changed.
     *
     * @param array|string $value
     */
    public function withValue($value): self
    {
        return new self($this->key, $value, $this->metadata);
    }

    /**
     * Returns a new instance of this secret with the metadata changed.
     */
    public function withMetadata(array $metadata): self
    {
        return new self($this->key, $this->value, $metadata);
    }
}
