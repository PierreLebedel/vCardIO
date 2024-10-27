<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Pleb\VCardIO\Exceptions\VCardCollectionArrayAccessException;
use Pleb\VCardIO\Exceptions\VCardCollectionIteratorException;
use Pleb\VCardIO\Models\AbstractVCard;

class VCardsCollection implements \ArrayAccess, \Countable, \Iterator
{
    protected int $iteratorKey;

    public array $versions = [];

    public function __construct(protected array $vCards = [])
    {
        $this->rewind();
    }

    public function getVCards(): array
    {
        return $this->vCards;
    }

    public function addVCard(AbstractVCard $vCard): self
    {
        $this->vCards[] = $vCard;

        if (! array_key_exists($vCard->version, $this->versions)) {
            $this->versions[$vCard->version] = 0;
        }

        $this->versions[$vCard->version]++;

        ksort($this->versions);

        return $this;
    }

    public function getVCard(int $index): VCard
    {
        if (! array_key_exists($index, $this->vCards)) {
            throw new \OutOfBoundsException('Invalid index');
        }

        return $this->vCards[$index];
    }

    public function first(): ?VCard
    {
        return $this->vCards[0] ?? null;
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
            throw VCardCollectionIteratorException::invalidIndex();
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
            throw VCardCollectionArrayAccessException::invalidIndex();
        }

        return $this->getVCard($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! is_int($offset)) {
            throw VCardCollectionArrayAccessException::invalidIndex('Invalid interger index');
        }
        if (! $value instanceof VCard) {
            throw VCardCollectionArrayAccessException::invalidValue('Invalid VCard value');
        }

        $this->vCards[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if (! $this->offsetExists($offset)) {
            throw VCardCollectionArrayAccessException::invalidIndex();
        }

        unset($this->vCards[$offset]);
    }
}
