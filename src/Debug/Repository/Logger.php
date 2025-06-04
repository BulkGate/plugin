<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Repository;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

interface Logger
{
	/**
	 * @param array<string, mixed> $parameters
	 */
	public function log(string $message, int $created, string $level = 'error', array $parameters = []): void;


	/**
	 * @return list<array{message: string, created: int, parameters: array<string, mixed>}>
	 */
	public function getList(string $level = 'error'): array;
}
