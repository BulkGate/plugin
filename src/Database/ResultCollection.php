<?php declare(strict_types=1);

namespace BulkGate\Plugin\Database;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use ArrayAccess, ArrayIterator, Countable, IteratorAggregate;
use function array_key_exists, count, is_array;

/**
 * @implements ArrayAccess<array-key, array<array-key, mixed>>
 * @implements IteratorAggregate<array-key, array<array-key, mixed>>
 */
class ResultCollection implements ArrayAccess, Countable, IteratorAggregate
{
	/**
	 * @var array<array-key, array<array-key, mixed>>
	 */
	private array $list;


	/**
	 * @param array<array-key, array<array-key, mixed>> $list
	 */
	public function __construct(array $list = [])
	{
		$this->list = $list;
	}


	/**
	 * @return ArrayIterator<array-key, array<array-key, mixed>>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->list);
	}


	/**
	 * @param array-key $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return array_key_exists($offset, $this->list);
	}

	/**
	 * @param array-key $offset
	 * @return array<array-key, mixed>|null
	 */
	public function offsetGet($offset): ?array
	{
		return $this->list[$offset] ?? null;
	}


	/**
	 * @param array-key|null $offset
	 * @param array<array-key, mixed> $value
	 */
	public function offsetSet($offset, $value): void
	{
		if (is_array($value))
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
	 * @param array-key $offset
	 */
	public function offsetUnset($offset): void
	{
		unset($this->list[$offset]);
	}


	public function count(): int
	{
		return count($this->list);
	}
}