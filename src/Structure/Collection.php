<?php declare(strict_types=1);

namespace BulkGate\Plugin\Structure;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use ArrayAccess, Countable, IteratorAggregate, ArrayIterator;
use function array_key_exists, count;

/**
 * @template TKey of array-key
 * @template TValue of Entity
 * @implements ArrayAccess<TKey, TValue>
 * @implements IteratorAggregate<TKey, TValue>
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate
{
	/**
	 * @var class-string<TValue>
	 */
	private string $type;

	/**
	 * @var array<array-key, TValue>
	 */
	private array $list;

	/**
	 * @param class-string<TValue> $type
	 * @param array<array-key, TValue> $list
	 */
	public function __construct(string $type, array $list = [])
	{
		$this->type = $type;
		$this->list = $list;
	}


	/**
	 * @param array-key $offset
	 */
	public function offsetExists($offset): bool
	{
		return array_key_exists($offset, $this->list);
	}


	/**
	 * @param TKey $offset
	 * @return TValue|null
	 */
	public function offsetGet($offset): ?Entity
	{
		return $this->list[$offset] ?? null;
	}


	/**
	 * @param TKey|null $offset
	 * @param TValue $value
	 */
	public function offsetSet($offset, $value): void
	{
		if ($value instanceof $this->type)
		{
			if ($offset === null)
			{
				$this->list[] = $value;
			}
			else
			{
				$this->list[$offset] = $value;
			}
		}
	}


	/**
	 * @param TKey $offset
	 */
	public function offsetUnset($offset): void
	{
		unset($this->list[$offset]);
	}


	/**
	 * @return ArrayIterator<TKey, TValue>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->list);
	}


	public function count(): int
	{
		return count($this->list);
	}


	/**
	 * @return  array<array-key, TValue>
	 */
	public function toArray(): array
	{
		return $this->list;
	}
}
