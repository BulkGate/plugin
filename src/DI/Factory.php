<?php declare(strict_types=1);

namespace BulkGate\Plugin\DI;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

interface Factory
{
	/**
	 * @param array<string, mixed> $parameters
	 * @return Container<object>
	 */
	public static function create(array $parameters = []): Container;
}
