<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

require __DIR__ . '/../bootstrap.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Event\Asynchronous, Event\Hook, Event\Repository\Asynchronous as AsynchronousRepository, Event\Repository\Entity\Task, Structure\Collection};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class AsynchronousTest extends TestCase
{
	public function testRun(): void
	{
		$repository = Mockery::mock(AsynchronousRepository::class);
		$repository->shouldReceive('load')->with(3)->andReturn(new Collection(Task::class, [
			new Task(['key' => 'task1', 'value' => '{"category": "test", "endpoint": "endpoint1", "variables": {"key": "value"}}']),
			new Task(['key' => 'task2', 'value' => '{"category": "test", "endpoint": "endpoint2", "variables": {"key2": "value2"}}']),
			new Task(['key' => 'task3', 'value' => '{"category": "test"}']),
		]));
		$repository->shouldReceive('finish')->with('task1')->once();
		$repository->shouldReceive('finish')->with('task2')->once();
		$repository->shouldReceive('finish')->with('task3')->once();

		$hook = Mockery::mock(Hook::class);
		$hook->shouldReceive('dispatch')->with('test', 'endpoint1', ['key' => 'value'])->once();
		$hook->shouldReceive('dispatch')->with('test', 'endpoint2', ['key2' => 'value2'])->once();

		$asynchronous = new Asynchronous($repository, $hook);

		Assert::same(2, $asynchronous->run(3));
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new AsynchronousTest())->run();
