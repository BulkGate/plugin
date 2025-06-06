<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Test;

require __DIR__ . '/../bootstrap.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Debug\Logger, Debug\Repository\Logger as Repository};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class LoggerTest extends TestCase
{
	public function testLog(): void
	{
		$logger = new Logger('6.3.0', '1.0.0', $repository = Mockery::mock(Repository::class));

		$repository->shouldReceive('log')->with('test', Mockery::type('int'), 'level', ['platform_version' => '6.3.0', 'module_version' => '1.0.0'])->once();

		$logger->log('test', 'level');

		Assert::true(true);
	}


	public function testGetList(): void
	{
		$logger = new Logger('6.3.0', '1.0.0', $repository = Mockery::mock(Repository::class));
		$repository->shouldReceive('getList')->with('error')->once()->ordered();
		$repository->shouldReceive('getList')->with('level')->once()->ordered();

		$logger->getList();
		$logger->getList('level');

		Assert::true(true);
	}

	public function testVersions(): void
	{
		$logger = new Logger('6.3.0', '1.0.0', Mockery::mock(Repository::class));

		Assert::same('6.3.0', $logger->platform_version);
		Assert::same('1.0.0', $logger->module_version);
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new LoggerTest())->run();
