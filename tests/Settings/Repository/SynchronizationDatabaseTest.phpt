<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Database, IO, Settings\Repository\SynchronizationDatabase};

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class SynchronizationDatabaseTest extends TestCase
{
	public function testLoadSettings(): void
	{
		$repository = new SynchronizationDatabase($db = Mockery::mock(Database\Connection::class), $io = Mockery::mock(IO\Connection::class));
		$db->shouldReceive('table')->with('bulkgate_module')->once()->andReturn('wp_bulkgate_module');
		$db->shouldReceive('execute')->with('SELECT * FROM `wp_bulkgate_module` WHERE `scope` NOT IN (\'static\', \'asynchronous\')')->once()->andReturn(new Database\ResultCollection([
			['scope' => 'server', 'key' => 'k1', 'type' => 'int', 'value' => '1', 'datetime' => 1681236226, 'order' => '1', 'synchronize_flag' => 'none'],
			['scope' => 'server', 'key' => 'k2', 'type' => 'string', 'value' => 'test', 'datetime' => 1681236226, 'order' => '2', 'synchronize_flag' => 'add'],
		]));
		$io->shouldReceive('run')->with(Mockery::on(function (IO\Request $request): bool
		{
			Assert::same('https://portal.bulkgate.com/api/v1/', $request->url);
			Assert::same('application/json', $request->content_type);
			Assert::same(30, $request->timeout);
			Assert::same('{"__synchronize":[{"scope":"server","key":"k1","type":"int","value":1,"datetime":1681236226,"order":1,"synchronize_flag":"none"},{"scope":"server","key":"k2","type":"string","value":"test","datetime":1681236226,"order":2,"synchronize_flag":"add"}]}', $request->serialize());

			return true;
		}))->once()->andReturn(new IO\Response('{
			"data": {
				"_generic": {
					"synchronize": [{
						"scope": "server",
						"key": "k1",
						"type": "int",
						"value": "1",
						"datetime": 1681236227,
						"order": 1,
						"synchronize_flag": "add"
					}]
				}
			}
		}', 'application/json'));

		$plugin = $repository->loadPluginSettings();

		Assert::same('server', $plugin['server:k1']->scope);
		Assert::same('k1', $plugin['server:k1']->key);
		Assert::same('int', $plugin['server:k1']->type);
		Assert::same(1, $plugin['server:k1']->value);
		Assert::same(1681236226, $plugin['server:k1']->datetime);
		Assert::same(1, $plugin['server:k1']->order);
		Assert::same('none', $plugin['server:k1']->synchronize_flag);

		Assert::same('server', $plugin['server:k2']->scope);
		Assert::same('k2', $plugin['server:k2']->key);
		Assert::same('string', $plugin['server:k2']->type);
		Assert::same('test', $plugin['server:k2']->value);
		Assert::same(1681236226, $plugin['server:k2']->datetime);
		Assert::same(2, $plugin['server:k2']->order);
		Assert::same('add', $plugin['server:k2']->synchronize_flag);

		$server = $repository->loadServerSettings('https://portal.bulkgate.com/api/v1/', $plugin, 30);

		Assert::same('server', $server['server:k1']->scope);
		Assert::same('k1', $server['server:k1']->key);
		Assert::same('int', $server['server:k1']->type);
		Assert::same(1, $server['server:k1']->value);
		Assert::same(1681236227, $server['server:k1']->datetime);
		Assert::same(1, $server['server:k1']->order);
		Assert::same('add', $server['server:k1']->synchronize_flag);
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new SynchronizationDatabaseTest())->run();
