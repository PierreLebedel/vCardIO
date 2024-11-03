<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Throwable;
use Pleb\VCardIO\Models\AbstractVCard;
use Pleb\VCardIO\Exceptions\VCardExportException;
use Pleb\VCardIO\Exceptions\VCardCollectionException;

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

    public function getVCard(int $index): AbstractVCard
    {
        if (! array_key_exists($index, $this->vCards)) {
            throw new \OutOfBoundsException('Invalid index');
        }

        return $this->vCards[$index];
    }

    public function first(): ?AbstractVCard
    {
        return $this->vCards[0] ?? null;
    }

    public function toString(): string
    {
        $collectionString = '';
        foreach ($this->vCards as $vCard) {
            $collectionString .= (string) $vCard;
        }

        return $collectionString;
    }

    public function __toString() :string
    {
        return $this->toString();
    }

    public function export(string $filePath, bool $append = false) :void
    {
        try{
            $mode = ($append) ? 'a' : 'w';

            $fp = fopen($filePath, $mode);

            if($mode=='a'){
                if(filesize($filePath)>0){
                    fwrite($fp, PHP_EOL);
                }
            }

            fwrite($fp, $this->toString());
            fclose($fp);

        } catch(Throwable $e){
            throw VCardExportException::unableToWrite($filePath);
        }
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
            throw VCardCollectionException::invalidIndex();
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
            throw VCardCollectionException::invalidIndex();
        }

        return $this->getVCard($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! is_int($offset)) {
            throw VCardCollectionException::invalidIndex('Invalid integer index');
        }
        if (! $value instanceof AbstractVCard) {
            throw VCardCollectionException::invalidValue('Invalid VCard value');
        }

        $this->vCards[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if (! $this->offsetExists($offset)) {
            throw VCardCollectionException::invalidIndex();
        }

        unset($this->vCards[$offset]);
    }
}
