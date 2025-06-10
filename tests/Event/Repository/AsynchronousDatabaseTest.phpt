<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Repository\Test;

require __DIR__ . '/../../bootstrap.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Database\Connection, Database\ResultCollection, Event\Repository\AsynchronousDatabase, Structure\Collection};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class AsynchronousDatabaseTest extends TestCase
{
	public function testLoadAndFinish(): void
	{
		$db = Mockery::mock(Connection::class);

		$db->shouldReceive('table')->with('bulkgate_module')->times(3)->andReturn('bulkgate_module');
		$db->shouldReceive('execute')->with('START TRANSACTION')->once()->ordered();
		$db->shouldReceive('execute')->with("SELECT * FROM `bulkgate_module` WHERE `scope` = 'asynchronous' AND `order` = 0 LIMIT 2 FOR UPDATE")->once()->ordered()->andReturn(new ResultCollection([
			['key' => 'task1', 'value' => '{"category": "test", "endpoint": "endpoint1", "variables": {"key": "value"}}', 'datetime' => '456', 'order' => '0'],
			['key' => 'task2', 'value' => '{"category": "test", "endpoint": "endpoint2", "variables": {"key2": "value2"}}', 'datetime' => 456, 'order' => 0],
		]));
		$db->shouldReceive('escape')->with('task1')->once()->ordered()->andReturn('escaped_task1');
		$db->shouldReceive('escape')->with('task2')->once()->ordered()->andReturn('escaped_task2');
		$db->shouldReceive('execute')->with("UPDATE `bulkgate_module` SET `order` = -1 WHERE `scope` = 'asynchronous' AND `key` IN ('escaped_task1','escaped_task2')")->once()->ordered();
		$db->shouldReceive('execute')->with('COMMIT')->once()->ordered();

		$db->shouldReceive('prepare')->with("DELETE FROM `bulkgate_module` WHERE `scope` = 'asynchronous' AND `key` = %s", 'test_key')->once()->ordered()->andReturn('SQL2');
		$db->shouldReceive('execute')->with('SQL2')->once();

		$asynchronousDatabase = new AsynchronousDatabase($db);
		$collection = $asynchronousDatabase->load(2);

		Assert::type(Collection::class, $collection);
		Assert::count(2, $collection);

		$asynchronousDatabase->finish('test_key');
	}


	public function testFail(): void
	{
		$db = Mockery::mock(Connection::class);

		$db->shouldReceive('table')->once()->andReturn('bulkgate_module');
		$db->shouldReceive('execute')->with('START TRANSACTION')->once()->ordered();
		$db->shouldReceive('execute')->with("SELECT * FROM `bulkgate_module` WHERE `scope` = 'asynchronous' AND `order` = 0 LIMIT 2 FOR UPDATE")->once()->ordered()->andReturnNull();
		$db->shouldReceive('execute')->with('ROLLBACK')->once()->ordered();

		$asynchronousDatabase = new AsynchronousDatabase($db);
		$collection = $asynchronousDatabase->load(2);

		Assert::type(Collection::class, $collection);
		Assert::count(0, $collection);
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new AsynchronousDatabaseTest())->run();
