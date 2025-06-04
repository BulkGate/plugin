<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

require __DIR__ . '/../bootstrap.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Event\Dispatcher, Event\Hook, Event\Loader, Event\Variables, Settings\Settings};
use function json_decode;

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


	public function testSpecificDirect(): void
	{
		$variables = new Variables();

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), $hook = Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, ['ok'])->once();
		$settings->shouldReceive('load')->with('main:synchronization')->once()->andReturn('message');
		$settings->shouldReceive('load')->with('main:address_preference')->once()->andReturn('invoice');
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('direct');
		$settings->shouldReceive('load')->with('main:language_mutation')->once()->andReturn('1');
		$settings->shouldReceive('load')->with('admin_sms-default-0:order_new')->once()->andReturn(json_decode('{"sms":{"active": true}}', true));
		$hook->shouldReceive('dispatch')->with('order', 'new', ['contact_synchronize' => 'message', 'contact_address_preference' => 'invoice'])->once();

		Assert::noError(fn () => $dispatcher->dispatch('order', 'new', $variables, ['ok']));
	}


	public function testSpecificAsset(): void
	{
		$variables = new Variables();
		$variables['lang_id'] = 'cs';
		$variables['order_status_id'] = 'delivered';

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), $hook = Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronization')->once()->andReturn('off');
		$settings->shouldReceive('load')->with('main:address_preference')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('direct');
		$settings->shouldReceive('load')->with('main:language_mutation')->once()->andReturn('1');
		$settings->shouldReceive('load')->with('admin_sms-default-0:order_status_change_delivered')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('customer_sms-cs-0:order_status_change_delivered')->once()->andReturn(json_decode('{"sms":{"active": true}}', true));
		$hook->shouldReceive('dispatch')->with('order', 'status_change', ['lang_id' => 'cs', 'order_status_id' => 'delivered', 'contact_synchronize' => 'off', 'contact_address_preference' => 'delivery'])->once();

		Assert::noError(fn () => $dispatcher->dispatch('order', 'status_change', $variables));
	}


	public function testSpecificAssetDefaultMutation(): void
	{
		$variables = new Variables();
		$variables['lang_id'] = 'cs';
		$variables['return_status_id'] = 'pending';

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), $hook = Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronization')->once()->andReturn('message');
		$settings->shouldReceive('load')->with('main:address_preference')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('direct');
		$settings->shouldReceive('load')->with('main:language_mutation')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('admin_sms-default-0:return_status_change_pending')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('customer_sms-default-0:return_status_change_pending')->once()->andReturn(json_decode('{"sms":{"active": true}}', true));
		$hook->shouldReceive('dispatch')->with('return', 'status_change', ['lang_id' => 'cs', 'return_status_id' => 'pending', 'contact_synchronize' => 'message', 'contact_address_preference' => 'delivery'])->once();

		Assert::noError(fn () => $dispatcher->dispatch('return', 'status_change', $variables));
	}


	public function testSpecificAssetNotFound(): void
	{
		$variables = new Variables();
		$variables['lang_id'] = 'cs';

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronization')->once()->andReturn('specific');
		$settings->shouldReceive('load')->with('main:address_preference')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('main:language_mutation')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('admin_sms-default-0:return_new')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('customer_sms-default-0:return_new')->once()->andReturnNull();

		Assert::noError(fn () => $dispatcher->dispatch('return', 'new', $variables));
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new DispatcherTest())->run();
