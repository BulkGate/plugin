<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Repository\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Debug\Repository\LoggerSettings, Settings\Settings};

require __DIR__ . '/../../bootstrap.php';

/**
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
			['message' => 'test3', 'created' => 7, 'parameters' => []]
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
			['message' => 'test4', 'created' => 8, 'parameters' => []]
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
			['message' => 'test4', 'created' => 8, 'parameters' => []]
		], ['type' => 'array'])->once();

		$repository->log('test4', 8, 'level');

		Assert::true(true);
	}


	public function testGetList(): void
	{
		$repository = new LoggerSettings($settings = Mockery::mock(Settings::class));
		$settings->shouldReceive('load')->with('static:log_level')->once()->andReturn([
			['message' => 'test1', 'created' => 5, 'parameters' => ['test1' => 'test1']],
			['message' => 'test2', 'created' => 6, 'parameters' => ['test2' => 'test2']],
			['message' => 'test3', 'created' => 7, 'parameters' => ['test3' => 'test3']],
			[],
			['message' => []],
			['created' => []]
		]);

		Assert::same([
			['message' => 'test1', 'created' => 5, 'parameters' => ['test1' => 'test1']],
			['message' => 'test2', 'created' => 6, 'parameters' => ['test2' => 'test2']],
			['message' => 'test3', 'created' => 7, 'parameters' => ['test3' => 'test3']],
			['message' => '', 'created' => 0, 'parameters' => []],
			['message' => '', 'created' => 0, 'parameters' => []],
			['message' => '', 'created' => 0, 'parameters' => []],
		], $repository->getList('level'));
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new LoggerSettingsTest())->run();
