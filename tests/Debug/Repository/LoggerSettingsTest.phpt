<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Repository\Test;

require __DIR__ . '/../../bootstrap.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Debug\Repository\LoggerSettings, Settings\Settings};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class LoggerSettingsTest extends TestCase
{
	public function testLogInLimit(): void
	{
		$repository = new LoggerSettings($settings = Mockery::mock(Settings::class));
		$settings->shouldReceive('load')->with('static:log_level')->once()->andReturn([
			['message' => 'test1', 'created' => 5],
			['message' => 'test2', 'created' => 6]
		]);
		$settings->shouldReceive('set')->with('static:log_level', [
			['message' => 'test1', 'created' => 5],
			['message' => 'test2', 'created' => 6],
			['message' => 'test3', 'created' => 7]
		], ['type' => 'array'])->once();

		$repository->log('test3', 7, 'level');

		Assert::true(true);
	}


	public function testLogOutLimit(): void
	{
		$repository = new LoggerSettings($settings = Mockery::mock(Settings::class));
		$repository->setup(3);
		$settings->shouldReceive('load')->with('static:log_level')->once()->andReturn([
			['message' => 'test1', 'created' => 5],
			['message' => 'test2', 'created' => 6],
			['message' => 'test3', 'created' => 7]
		]);
		$settings->shouldReceive('set')->with('static:log_level', [
			['message' => 'test2', 'created' => 6],
			['message' => 'test3', 'created' => 7],
			['message' => 'test4', 'created' => 8]
		], ['type' => 'array'])->once();

		$repository->log('test4', 8, 'level');

		Assert::true(true);
	}


	public function testInvalid(): void
	{
		$repository = new LoggerSettings($settings = Mockery::mock(Settings::class));
		$repository->setup(3);
		$settings->shouldReceive('load')->with('static:log_level')->once()->andReturnNull();
		$settings->shouldReceive('set')->with('static:log_level', [
			['message' => 'test4', 'created' => 8]
		], ['type' => 'array'])->once();

		$repository->log('test4', 8, 'level');

		Assert::true(true);
	}


	public function testGetList(): void
	{
		$repository = new LoggerSettings($settings = Mockery::mock(Settings::class));
		$settings->shouldReceive('load')->with('static:log_level')->once()->andReturn([
			['message' => 'test1', 'created' => 5],
			['message' => 'test2', 'created' => 6],
			['message' => 'test3', 'created' => 7],
			[],
			['message' => []],
			['created' => []]
		]);

		Assert::same([
			['message' => 'test1', 'created' => 5],
			['message' => 'test2', 'created' => 6],
			['message' => 'test3', 'created' => 7],
			['message' => '', 'created' => 0],
			['message' => '', 'created' => 0],
			['message' => '', 'created' => 0],
		], $repository->getList('level'));
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new LoggerSettingsTest())->run();
