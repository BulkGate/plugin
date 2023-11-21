<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Debug\Logger, InvalidResponseException, IO\Connection, IO\Request, IO\Url, Event\Hook, Event\Variables};

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class HookTest extends TestCase
{
	public function testDispatch(): void
	{
		$version = '1.0';

		$hook = new Hook($version, $connection = Mockery::mock(Connection::class), new Url(), Mockery::mock(Logger::class));

		$connection->shouldReceive('run')->once()->with(Mockery::on(function (Request $request): bool
		{
			Assert::same('{"language":"cs","shop_id":1,"variables":{"test":"test","shop_id":1,"lang_id":"cs"}}', $request->serialize());
			Assert::same('https://portal.bulkgate.com/api/1.0/eshop/order/new', $request->url);

			return true;
		}));

		$variables = new Variables();
		$variables['test'] = 'test';
		$variables['shop_id'] = 1;
		$variables['lang_id'] = 'cs';

		$hook->dispatch('order', 'new', $variables->toArray());

		Assert::true(true);
	}


	public function testError(): void
	{
		$version = '1.0';

		$hook = new Hook($version, $connection = Mockery::mock(Connection::class), new Url(), $logger = Mockery::mock(Logger::class));
		$connection->shouldReceive('run')->once()->andThrow(InvalidResponseException::class, 'error_test');
		$logger->shouldReceive('log')->with('Hook Error: \'api/1.0/eshop/order/new\' - error_test, {"language":null,"shop_id":null,"variables":{"test":"test"}}')->once();

		$variables = new Variables();
		$variables['test'] = 'test';

		$hook->dispatch('order', 'new', $variables->toArray());

		Assert::true(true);
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new HookTest())->run();
