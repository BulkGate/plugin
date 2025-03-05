<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Structure\Collection, Settings\Repository\Entity\Setting, Settings\Repository\Settings as Repository, Settings\Settings};

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SettingsTest extends TestCase
{
	public function testLoad(): void
	{
		$settings = new Settings($repository = Mockery::mock(Repository::class));
		$repository->shouldReceive('load')->with('main')->twice()->andReturn(new Collection(Setting::class, [
			'one' => $one = new Setting(['scope' => 'main', 'key' => 'one', 'type' => 'string', 'value' => 'v1']),
			'two' => $two = new Setting(['scope' => 'main', 'key' => 'one', 'type' => 'array', 'value' => '{"value":"v2"}']),
		]));

		Assert::same('v1', $settings->load('main:one'));
		Assert::same(['value' => 'v2'], $settings->load('main:two'));
		Assert::null($settings->load('main:three'));
		Assert::same(['one' => $one, 'two' => $two], $settings->load('main:'));

		Assert::same(['one' => $one, 'two' => $two], $settings->load('main:', true));

		$settings->setDefaultSettings(['main:three' => 'v3', 'xx|:' => 'b']);

		Assert::same('v3', $settings->load('main:three'));
	}


	public function testSet(): void
	{
		$settings = new Settings($repository = Mockery::mock(Repository::class));
		$repository->shouldReceive('save')->with(Mockery::on(function (Setting $setting): bool
		{
			Assert::same('static', $setting->scope);
			Assert::same('LGC', $setting->key);
			Assert::same('int', $setting->type);
			Assert::same(451, $setting->value);
			Assert::type('int', $setting->datetime);
			Assert::same(0, $setting->order);
			Assert::same('change', $setting->synchronize_flag);

			return true;
		}))->once();

		$settings->set('static:LGC', 451, ['synchronize_flag' => 'change']);

		Assert::true(true);
	}


	public function testDelete(): void
	{
		$settings = new Settings($repository = Mockery::mock(Repository::class));
		$repository->shouldReceive('remove')->with('static', 'test')->once();

		$settings->delete('static:test');
	}


	public function testCleanup(): void
	{
		$settings = new Settings($repository = Mockery::mock(Repository::class));
		$repository->shouldReceive('cleanup')->withNoArgs()->once();

		$settings->cleanup();
	}


	public function testInstall(): void
	{
		$settings = new Settings($repository = Mockery::mock(Repository::class));
		$repository->shouldReceive('createTable')->withNoArgs()->once();
		$repository->shouldReceive('save')->with(Mockery::on(function (Setting $setting): bool
		{
			Assert::same('static', $setting->scope);
			Assert::same('synchronize', $setting->key);
			Assert::same('int', $setting->type);
			Assert::same(0, $setting->value);
			Assert::type('int', $setting->datetime);
			Assert::same(0, $setting->order);
			Assert::same('none', $setting->synchronize_flag);

			return true;
		}))->once();

		$settings->install();
	}


	public function testUninstall(): void
	{
		$settings = new Settings($repository = Mockery::mock(Repository::class));
		$repository->shouldReceive('load')->with('main')->once()->andReturn(new Collection(Setting::class, [
			'delete_db' => new Setting(['scope' => 'main', 'key' => 'delete_db', 'type' => 'bool', 'value' => '1']),
		]));
		$repository->shouldReceive('dropTable')->withNoArgs()->once();
		$repository->shouldReceive('remove')->with('static', 'application_token')->once();


		$settings->uninstall();
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new SettingsTest())->run();
