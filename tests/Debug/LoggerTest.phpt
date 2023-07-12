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
		$logger = new Logger($repository = Mockery::mock(Repository::class));

		$repository->shouldReceive('log')->with('test', Mockery::type('int'), 'level')->once();

		$logger->log('test', 'level');

		Assert::true(true);
	}


	public function testGetList(): void
	{
		$logger = new Logger($repository = Mockery::mock(Repository::class));
		$repository->shouldReceive('getList')->with('level')->once()->andReturn([
			['message' => 'test1', 'created' => 5],
			['message' => 'test2', 'created' => 6]
		]);

		Assert::same([
			['message' => 'test1', 'created' => 5],
			['message' => 'test2', 'created' => 6]
		], $logger->getList('level'));
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new LoggerTest())->run();
