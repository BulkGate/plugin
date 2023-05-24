<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{IO\Connection, IO\Request, IO\Url, Event\Hook, Event\Variables};

require_once __DIR__ . '/../bootstrap.php';

class HookTest extends TestCase
{
	public function testDispatch(): void
	{
		$version = '1.0';

		$hook = new Hook($version, $connection = Mockery::mock(Connection::class), new Url());

		$connection->shouldReceive('run')->once()->with(Mockery::on(function (Request $request): bool
		{
			Assert::same('{"variables":{"test":"test"}}', $request->serialize());
			Assert::same('https://portal.bulkgate.com/api/1.0/eshop/order/new', $request->url);

			return true;
		}));

		$variables = new Variables();
		$variables['test'] = 'test';

		$hook->dispatch('order', 'new', $variables->toArray());

		Assert::true(true);
	}
}

(new HookTest())->run();
