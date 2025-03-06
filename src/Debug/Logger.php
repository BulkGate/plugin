<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\Strict;

class Logger
{
	use Strict;

	private string $platform_version;

	private string $module_version;

	private Repository\Logger $repository;

	public function __construct(string $platform_version, string $module_version, Repository\Logger $repository)
	{
		$this->repository = $repository;
		$this->platform_version = $platform_version;
		$this->module_version = $module_version;
	}


	public function log(string $message, string $level = 'error'): void
	{
		$this->repository->log($message, time(), $level, [
			'platform_version' => $this->platform_version,
			'module_version' => $this->module_version,
		]);
	}


	/**
	 * @return list<array{message: string, created: int, parameters: array<string, mixed>}>>
	 */
	public function getList(string $level = 'error'): array
	{
		return $this->repository->getList($level);
	}
}
