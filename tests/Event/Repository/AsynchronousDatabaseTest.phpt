<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Repository\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\Database\Connection;
use BulkGate\Plugin\Event\Repository\AsynchronousDatabase;
use BulkGate\Plugin\Structure\Collection;
use Mockery;
use Tester\Assert;
use Tester\TestCase;
use function str_replace;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class AsynchronousDatabaseTest extends TestCase
{
	public function testLoadAndFinish(): void
	{
		$db = Mockery::mock(Connection::class);

		$db->shouldReceive('table')->andReturn('bulkgate_module');
		$db->shouldReceive('prepare')->andReturnUsing(fn (string $query) => str_replace('%s', '\'ok\'', $query));

		$db->shouldReceive('execute')->with('START TRANSACTION')->once()->ordered();
		$db->shouldReceive('execute')->with("SELECT * FROM `bulkgate_module` WHERE `scope` = 'asynchronous' AND `order` = 0 LIMIT 2 FOR UPDATE")->once()->andReturn([
			['key' => 'task1', 'value' => '{"category": "test", "endpoint": "endpoint1", "variables": {"key": "value"}}'],
			['key' => 'task2', 'value' => '{"category": "test", "endpoint": "endpoint2", "variables": {"key2": "value2"}}'],
		])->ordered();
		$db->shouldReceive('escape')->with('task1')->once()->ordered()->andReturn('escaped_task1');
		$db->shouldReceive('escape')->with('task2')->once()->ordered()->andReturn('escaped_task2');
		$db->shouldReceive('execute')->with("UPDATE `bulkgate_module` SET `order` = -1 WHERE `scope` = 'asynchronous' AND `key` IN ('escaped_task1','escaped_task2')")->once()->ordered();
		$db->shouldReceive('execute')->with('COMMIT')->once()->ordered();
		$db->shouldReceive('execute')->with("DELETE FROM `bulkgate_module` WHERE `scope` = 'asynchronous' AND `key` = 'ok'")->once()->ordered();

		$asynchronousDatabase = new AsynchronousDatabase($db);

		$collection = $asynchronousDatabase->load(2);

		Assert::type(Collection::class, $collection);
		Assert::count(2, $collection);

		$asynchronousDatabase->finish('test_key');

		Mockery::close();
	}


	public function testFail(): void
	{
		$db = Mockery::mock(Connection::class);

		$db->shouldReceive('table')->andReturn('bulkgate_module');
		$db->shouldReceive('prepare')->andReturnUsing(fn(string $query) => str_replace('%s', '\'ok\'', $query));

		$db->shouldReceive('execute')->with('START TRANSACTION')->once()->ordered();
		$db->shouldReceive('execute')->with("SELECT * FROM `bulkgate_module` WHERE `scope` = 'asynchronous' AND `order` = 0 LIMIT 2 FOR UPDATE")->once()->andReturnNull()->ordered();
		$db->shouldReceive('execute')->with('ROLLBACK')->once()->ordered();

		$asynchronousDatabase = new AsynchronousDatabase($db);

		$collection = $asynchronousDatabase->load(2);

		Assert::type(Collection::class, $collection);
		Assert::count(0, $collection);

		Mockery::close();
	}
}

(new AsynchronousDatabaseTest())->run();
