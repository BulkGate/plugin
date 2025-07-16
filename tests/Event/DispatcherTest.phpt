<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

require __DIR__ . '/../bootstrap.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Event\Dispatcher, Event\Hook, Event\Loader, Event\Variables, Settings\Settings};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class DispatcherTest extends TestCase
{
	public function testAllCron(): void
	{
		$variables = new Variables();

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronization')->once()->andReturn('all');
		$settings->shouldReceive('load')->with('main:address_preference')->once()->andReturn('invoice');
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('cron');
		$settings->shouldReceive('set')->with(Mockery::on(function (string $key): bool
		{
			Assert::match('~^asynchronous:[\w_-]+~', $key);

			return true;
		}), ['category' => 'order', 'endpoint' => 'new', 'variables' => ['contact_synchronize' => 'all', 'contact_address_preference' => 'invoice']], ['type' => 'json'])->once();

		$test = false;
		$dispatcher->dispatch('order', 'new', $variables, [], function () use (&$test): void
		{
			$test = true;
		});

		Assert::true($test);
	}


	public function testAllAsset(): void
	{
		$variables = new Variables();

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronization')->once()->andReturn('all');
		$settings->shouldReceive('load')->with('main:address_preference')->once()->andReturn('delivery');
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('asset');
		$settings->shouldReceive('set')->with(Mockery::on(function (string $key): bool
		{
			Assert::match('~^asynchronous:[\w_-]+~', $key);

			return true;
		}), ['category' => 'order', 'endpoint' => 'new', 'variables' => ['contact_synchronize' => 'all', 'contact_address_preference' => 'delivery']], ['type' => 'json'])->once();

		Assert::noError(fn () => $dispatcher->dispatch('order', 'new', $variables));
	}


	public function testAllDirect(): void
	{
		$variables = new Variables();

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), $hook = Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronization')->once()->andReturn('all');
		$settings->shouldReceive('load')->with('main:address_preference')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('direct');
		$hook->shouldReceive('dispatch')->with('order', 'new', ['contact_synchronize' => 'all', 'contact_address_preference' => 'delivery'])->once();

		Assert::noError(fn () => $dispatcher->dispatch('order', 'new', $variables));
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new DispatcherTest())->run();
