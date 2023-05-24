<?php declare(strict_types=1);

namespace BulkGate\Plugin\Database;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

interface Connection
{
	/**
	 * @param literal-string $sql
	 * @return array<array-key, array<array-key, mixed>>|null
	 */
	public function execute(string $sql): ?array;


	/**
	 * @param literal-string $sql
	 * @param mixed ...$parameters
	 * @return literal-string
	 */
	public function prepare(string $sql, ...$parameters): string;


	/**
	 * @return int|string
	 */
	public function lastId();


	/**
	 * @return literal-string
	 */
	public function escape(string $string): string;


	/**
	 * @return literal-string
	 */
	public function prefix(): string;


	/**
	 * @return array<array-key, string>
	 */
	public function getSqlList(): array;


	/**
	 * @param literal-string $table
	 * @return literal-string
	 */
	public function table(string $table): string;
}
