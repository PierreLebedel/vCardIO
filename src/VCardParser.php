<?php

namespace Pleb\VCardIO;

use Pleb\VCardIO\Exceptions\VCardIOArrayAccessException;
use Pleb\VCardIO\Exceptions\VCardIOIteratorException;
use Pleb\VCardIO\Exceptions\VCardIOParserException;
use Pleb\VCardIO\Models\VCard;

class VCardParser implements \ArrayAccess, \Countable, \Iterator
{
    protected array $vCards;

    protected int $iteratorKey;

    public function __construct(string $rawData)
    {
        $this->rewind();

        $this->vCards = [
            new VCard,
            new VCard,
        ];
    }

    public static function parseFile(string $filePath): self
    {
        if (! file_exists($filePath)) {
            throw VCardIOParserException::fileNotFound($filePath);
        }
        if (! is_readable($filePath)) {
            throw VCardIOParserException::fileUnreadable($filePath);
        }

        return self::parseRaw(file_get_contents($filePath));
    }

    public static function parseRaw(string $rawData): self
    {
        return new self($rawData);
    }

    public function getVCards(): array
    {
        return $this->vCards;
    }

    public function getVCard(int $index): VCard
    {
        if (! array_key_exists($index, $this->vCards)) {
            throw new \OutOfBoundsException('Invalid index');
        }

        return $this->vCards[$index];
    }

    /**
     * Countable interface implementation
     */
    public function count(): int
    {
        return count($this->vCards);
    }

    /**
     * Iterator interface implementation
     */
    public function rewind(): void
    {
        $this->iteratorKey = 0;
    }

    public function current(): mixed
    {
        if (! $this->valid()) {
            throw VCardIOIteratorException::invalidIndex();
        }

        return $this->vCards[$this->key()] ?? null;
    }

    public function key(): mixed
    {
        return $this->iteratorKey;
    }

    public function next(): void
    {
        $this->iteratorKey++;
    }

    public function valid(): bool
    {
        return array_key_exists($this->key(), $this->vCards);
    }

    /**
     * ArrayAccess interface implementation
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->vCards);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (! $this->offsetExists($offset)) {
            throw VCardIOArrayAccessException::invalidIndex();
        }

        return $this->getVCard($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! is_int($offset)) {
            throw VCardIOArrayAccessException::invalidIndex('Invalid interger index');
        }
        if (! $value instanceof VCard) {
            throw VCardIOArrayAccessException::invalidValue('Invalid VCard value');
        }

        $this->vCards[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if (! $this->offsetExists($offset)) {
            throw VCardIOArrayAccessException::invalidIndex();
        }

        unset($this->vCards[$offset]);
    }
}
