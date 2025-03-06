<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Debug\Logger, Debug\Repository\Logger as Repository};

require __DIR__ . '/../bootstrap.php';

/**
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


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new LoggerTest())->run();
