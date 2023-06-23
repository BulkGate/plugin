<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{AuthenticateException, Eshop\Configuration, InvalidResponseException, IO\Url, Settings\Repository\Synchronization, Settings\Synchronizer, Structure\Collection, Settings\Repository\Entity\Setting, Settings\Settings};
use function time;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SynchronizerTest extends TestCase
{
	public function testSynchronize(): void
	{
		$synchronizer = new Synchronizer($repository = Mockery::mock(Synchronization::class), $settings = Mockery::mock(Settings::class), new Url(), $configuration = Mockery::mock(Configuration::class));
		$repository->shouldReceive('loadPluginSettings')->once()->withNoArgs()->andReturn($plugin = new Collection(Setting::class, [
			'main:key1' => new Setting(['scope' => 'main', 'key' => 'key1', 'value' => 'value1', 'datetime' => 150]),
			'main:key2' => new Setting(['scope' => 'main', 'key' => 'key2', 'value' => 'value2', 'datetime' => 150]),
		]));
		$repository->shouldReceive('loadServerSettings')->with('https://portal.bulkgate.com/module/settings/synchronize', $plugin, 6)->once()->andReturn(new Collection(Setting::class, [
			new Setting(['scope' => 'main', 'key' => 'key1', 'value' => 'value1', 'datetime' => 50]),
			new Setting(['scope' => 'main', 'key' => 'key2', 'value' => 'value10', 'datetime' => 200]),
			new Setting(['scope' => 'main', 'key' => 'key3', 'value' => 'value3', 'datetime' => 300]),
		]));
		$settings->shouldReceive('load')->with('static:version')->once()->andReturn('1.0.0');
		$configuration->shouldReceive('version')->withNoArgs()->once()->andReturn('1.0.0');
		$settings->shouldReceive('load')->with('static:synchronize')->once()->andReturn(time() - 100);
		$settings->shouldReceive('load')->with('static:application_id')->once()->andReturn(451);
		$settings->shouldReceive('set')->with('main:key2', 'value10', ['type' => 'string', 'datetime' => 200, 'order' => 0, 'synchronize_flag' => 'none'])->once();
		$settings->shouldReceive('set')->with('main:key3', 'value3', ['type' => 'string', 'datetime' => 300, 'order' => 0, 'synchronize_flag' => 'none'])->once();
		$settings->shouldReceive('load')->with('main:synchronize_interval')->once()->andReturn(700);
		$settings->shouldReceive('set')->with('static:synchronize', Mockery::type('int'), ['type' => 'int'])->once();
		$settings->shouldReceive('cleanup')->withNoArgs()->once();

		$synchronizer->synchronize();

		Assert::true(true);
	}


	public function testExceptions(): void
	{
		$synchronizer = new Synchronizer($repository = Mockery::mock(Synchronization::class), $settings = Mockery::mock(Settings::class), new Url(), $configuration = Mockery::mock(Configuration::class));
		$repository->shouldReceive('loadPluginSettings')->withNoArgs()->twice()->andReturn($plugin = new Collection(Setting::class, [
			'main:key1' => new Setting(['scope' => 'main', 'key' => 'key1', 'value' => 'value1', 'datetime' => 150]),
			'main:key2' => new Setting(['scope' => 'main', 'key' => 'key2', 'value' => 'value2', 'datetime' => 150]),
		]));
		$settings->shouldReceive('load')->with('static:version')->twice()->andReturn('1.0.0');
		$configuration->shouldReceive('version')->withNoArgs()->times(4)->andReturn('2.0.0');
		$settings->shouldReceive('install')->with(true)->twice();
		$settings->shouldReceive('set')->with('static:version', '2.0.0', ['type' => 'string'])->twice();
		$repository->shouldReceive('loadServerSettings')->with('https://portal.bulkgate.com/module/settings/synchronize', $plugin, 6)->once()->andThrow(AuthenticateException::class);
		$repository->shouldReceive('loadServerSettings')->with('https://portal.bulkgate.com/module/settings/synchronize', $plugin, 6)->once()->andThrow(InvalidResponseException::class);
		$settings->shouldReceive('delete')->with('static:application_token')->once();
		$synchronizer->synchronize(true);
		$synchronizer->synchronize(true);

		Assert::true(true);
	}


	public function testGetLastSync(): void
	{
		$synchronizer = new Synchronizer(Mockery::mock(Synchronization::class), $settings = Mockery::mock(Settings::class), new Url(), Mockery::mock(Configuration::class));
		$settings->shouldReceive('load')->with('static:synchronize')->once()->andReturn(501);
		$settings->shouldReceive('load')->with('main:synchronize_interval')->once()->andReturn(50);

		Assert::same(451, $synchronizer->getLastSync());
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new SynchronizerTest())->run();
