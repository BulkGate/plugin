<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, Expect, TestCase};
use BulkGate\Plugin\{Database\Connection, Settings\Repository\Entity\Setting, Settings\Repository\SettingsDatabase};

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class SettingsDatabaseTest extends TestCase
{
	public function testLoad(): void
	{
		$repository = new SettingsDatabase($connection = Mockery::mock(Connection::class));
		$connection->shouldReceive('table')->with('bulkgate_module')->once()->andReturn('prefix_bulkgate_module');
		$connection->shouldReceive('prepare')
			->with('SELECT * FROM `prefix_bulkgate_module` WHERE `scope` = %s AND `synchronize_flag` != %s ORDER BY `order`', 'main', 'delete')
			->once()
			->andReturn($sql = 'SELECT * FROM `prefix_bulkgate_module` WHERE `scope` = \'main\' AND `synchronize_flag` != \'delete\' ORDER BY `order`');

		$connection->shouldReceive('execute')->with($sql)->once()->andReturn([
			['scope' => 'main', 'key' => 'one', 'type' => 'string', 'value' => 'v1'],
			['scope' => 'main', 'key' => 'two', 'type' => 'array', 'value' => '{"value":"v2"}']
		]);

		$list = $repository->load('main')->toArray();

		Assert::equal([
			'scope' => 'main',
			'key' => 'one',
			'type' => 'string',
			'value' => 'v1',
			'datetime' => Expect::type('int'),
			'order' => 0,
			'synchronize_flag' => 'none',
		], (array) $list['one']);

		Assert::equal([
			'scope' => 'main',
			'key' => 'two',
			'type' => 'array',
			'value' => ['value' => 'v2'],
			'datetime' => Expect::type('int'),
			'order' => 0,
			'synchronize_flag' => 'none',
		], (array) $list['two']);
	}


	public function testSave(): void
	{
		$repository = new SettingsDatabase($connection = Mockery::mock(Connection::class));
		$connection->shouldReceive('table')->with('bulkgate_module')->once()->andReturn('prefix_bulkgate_module');
		$connection->shouldReceive('prepare')
			->with('INSERT INTO `prefix_bulkgate_module` (`scope`, `key`, `type`, `value`, `datetime`, `order`, `synchronize_flag`) VALUES (%s, %s, %s, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE `type` = VALUES(`type`), `value` = VALUES(`value`), `datetime` = VALUES(`datetime`), `order` = VALUES(`order`), `synchronize_flag` = VALUES(`synchronize_flag`)', 'main', 'one', 'string', 'v1', 1669365608, 0, 'none')
			->once()
			->andReturn($sql = 'INSERT INTO `prefix_bulkgate_module` (`scope`, `key`, `type`, `value`, `datetime`, `order`, `synchronize_flag`) VALUES (\'main\', \'one\', \'string\', \'v1\', 1669365608, 0, \'none\') ON DUPLICATE KEY UPDATE `type` = VALUES(`type`), `value` = VALUES(`value`), `datetime` = VALUES(`datetime`), `order` = VALUES(`order`), `synchronize_flag` = VALUES(`synchronize_flag`)');
		$connection->shouldReceive('execute')->with($sql)->once()->andReturnNull();

		$repository->save(new Setting(['scope' => 'main', 'key' => 'one', 'type' => 'string', 'datetime' => 1669365608, 'value' => 'v1']));

		Assert::true(true);
	}


	public function testRemove(): void
	{
		$repository = new SettingsDatabase($connection = Mockery::mock(Connection::class));
		$connection->shouldReceive('table')->with('bulkgate_module')->once()->andReturn('prefix_bulkgate_module');
		$connection->shouldReceive('prepare')->with('DELETE FROM `prefix_bulkgate_module` WHERE `scope` = %s AND `key` = %s', 'main', 'test')->once()->andReturn($sql = 'DELETE FROM `prefix_bulkgate_module` WHERE `scope` = \'main\' AND `key` = \'test\'');
		$connection->shouldReceive('execute')->with($sql)->once()->andReturnNull();

		$repository->remove('main', 'test');

		Assert::true(true);
	}


	public function testCleanup(): void
	{
		$repository = new SettingsDatabase($connection = Mockery::mock(Connection::class));
		$connection->shouldReceive('table')->with('bulkgate_module')->once()->andReturn('prefix_bulkgate_module');
		$connection->shouldReceive('execute')->with('DELETE FROM `prefix_bulkgate_module` WHERE `synchronize_flag` = \'delete\'')->once()->andReturnNull();

		$repository->cleanup();

		Assert::true(true);
	}


	public function testCreateTable(): void
	{
		$repository = new SettingsDatabase($connection = Mockery::mock(Connection::class));
		$connection->shouldReceive('table')->with('bulkgate_module')->twice()->andReturn('prefix_bulkgate_module');
		$connection->shouldReceive('execute')->with('CREATE TABLE IF NOT EXISTS `prefix_bulkgate_module` (`scope` varchar(50) NOT NULL DEFAULT \'main\',`key` varchar(50) NOT NULL,`type` varchar(50) NOT NULL DEFAULT \'string\',`value` longtext DEFAULT NULL,`datetime` int(11) NOT NULL DEFAULT unix_timestamp(current_timestamp()),`order` int(11) NOT NULL DEFAULT 0,`synchronize_flag` varchar(50) NOT NULL DEFAULT \'none\' COMMENT \'none/add/change/delete\',PRIMARY KEY (`scope`,`key`),KEY `synchronize_flag` (`synchronize_flag`),KEY `scope_synchronize_flag` (`scope`,`synchronize_flag`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;')->once()->andReturnNull();
		$connection->shouldReceive('execute')->with('ALTER TABLE `prefix_bulkgate_module` ENGINE=InnoDB;')->once();

		$repository->createTable();

		Assert::true(true);
	}


	public function testDropTable(): void
	{
		$repository = new SettingsDatabase($connection = Mockery::mock(Connection::class));
		$connection->shouldReceive('table')->with('bulkgate_module')->once()->andReturn('prefix_bulkgate_module');
		$connection->shouldReceive('execute')->with('DROP TABLE IF EXISTS `prefix_bulkgate_module`')->once()->andReturnNull();

		$repository->dropTable();

		Assert::true(true);
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new SettingsDatabaseTest())->run();
