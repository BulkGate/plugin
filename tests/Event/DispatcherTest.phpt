<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Event\Dispatcher, Event\Hook, Event\Loader, Event\Variables, Settings\Settings};
use function json_decode;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class DispatcherTest extends TestCase
{
	public function testAllCron(): void
	{
		$variables = new Variables();

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronize')->once()->andReturn('all');
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('cron');
		$settings->shouldReceive('set')->with(Mockery::on(function (string $key): bool
		{
			Assert::match('~^asynchronous:[\w_-]+~', $key);

			return true;
		}), ['category' => 'order', 'endpoint' => 'new', 'variables' => ['contact_synchronize' => 'all']], ['type' => 'json'])->once();

		$dispatcher->dispatch('order', 'new', $variables);

		Mockery::close();
	}


	public function testAllAsset(): void
	{
		$variables = new Variables();

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronize')->once()->andReturn('all');
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('asset');
		$settings->shouldReceive('set')->with(Mockery::on(function (string $key): bool
		{
			Assert::match('~^asynchronous:[\w_-]+~', $key);

			return true;
		}), ['category' => 'order', 'endpoint' => 'new', 'variables' => ['contact_synchronize' => 'all']], ['type' => 'json'])->once();

		$dispatcher->dispatch('order', 'new', $variables);

		Mockery::close();
	}


	public function testAllDirect(): void
	{
		$variables = new Variables();

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), $hook = Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronize')->once()->andReturn('all');
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('direct');
		$hook->shouldReceive('dispatch')->with('order', 'new', ['contact_synchronize' => 'all'])->once();

		$dispatcher->dispatch('order', 'new', $variables);

		Mockery::close();
	}


	public function testSpecificDirect(): void
	{
		$variables = new Variables();

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), $hook = Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, ['ok'])->once();
		$settings->shouldReceive('load')->with('main:synchronize')->once()->andReturn('message');
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('direct');
		$settings->shouldReceive('load')->with('main:language_mutation')->once()->andReturn('1');
		$settings->shouldReceive('load')->with('admin_sms-default-0:order_new')->once()->andReturn(json_decode('{"sms":{"active": true}}', true));
		$hook->shouldReceive('dispatch')->with('order', 'new', ['contact_synchronize' => 'message'])->once();

		$dispatcher->dispatch('order', 'new', $variables, ['ok']);

		Mockery::close();
	}


	public function testSpecificAsset(): void
	{
		$variables = new Variables();
		$variables['lang_id'] = 'cs';
		$variables['order_status_id'] = 'delivered';

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), $hook = Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronize')->once()->andReturn('off');
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('direct');
		$settings->shouldReceive('load')->with('main:language_mutation')->once()->andReturn('1');
		$settings->shouldReceive('load')->with('admin_sms-default-0:order_status_change_delivered')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('customer_sms-cs-0:order_status_change_delivered')->once()->andReturn(json_decode('{"sms":{"active": true}}', true));
		$hook->shouldReceive('dispatch')->with('order', 'status_change', ['lang_id' => 'cs', 'order_status_id' => 'delivered', 'contact_synchronize' => 'off'])->once();

		$dispatcher->dispatch('order', 'status_change', $variables);

		Mockery::close();
	}


	public function testSpecificAssetDefaultMutation(): void
	{
		$variables = new Variables();
		$variables['lang_id'] = 'cs';
		$variables['return_status_id'] = 'pending';

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), $hook = Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronize')->once()->andReturn('message');
		$settings->shouldReceive('load')->with('main:dispatcher')->once()->andReturn('direct');
		$settings->shouldReceive('load')->with('main:language_mutation')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('admin_sms-default-0:return_status_change_pending')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('customer_sms-default-0:return_status_change_pending')->once()->andReturn(json_decode('{"sms":{"active": true}}', true));
		$hook->shouldReceive('dispatch')->with('return', 'status_change', ['lang_id' => 'cs', 'return_status_id' => 'pending', 'contact_synchronize' => 'message'])->once();

		$dispatcher->dispatch('return', 'status_change', $variables);

		Mockery::close();
	}


	public function testSpecificAssetNotFound(): void
	{
		$variables = new Variables();
		$variables['lang_id'] = 'cs';

		$dispatcher = new Dispatcher($settings = Mockery::mock(Settings::class), Mockery::mock(Hook::class), $loader = Mockery::mock(Loader::class));
		$loader->shouldReceive('load')->with($variables, [])->once();
		$settings->shouldReceive('load')->with('main:synchronize')->once()->andReturn('specific');
		$settings->shouldReceive('load')->with('main:language_mutation')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('admin_sms-default-0:return_new')->once()->andReturnNull();
		$settings->shouldReceive('load')->with('customer_sms-default-0:return_new')->once()->andReturnNull();

		$dispatcher->dispatch('return', 'new', $variables);

		Mockery::close();
	}
}

(new DispatcherTest())->run();
