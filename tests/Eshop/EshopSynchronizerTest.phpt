<?php declare(strict_types=1);

namespace BulkGate\Plugin\Eshop\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Eshop\Language, Eshop\MultiStore, Eshop\OrderStatus, Eshop\ReturnStatus, Settings\Settings, Eshop\EshopSynchronizer, Settings\Synchronizer};

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class EshopSynchronizerTest extends TestCase
{
	public function testRun(): void
	{
		$eshop_synchronizer = new EshopSynchronizer(
			$synchronizer = Mockery::mock(Synchronizer::class),
			$settings = Mockery::mock(Settings::class),
			$order_status = Mockery::mock(OrderStatus::class),
			$return_status = Mockery::mock(ReturnStatus::class),
			$languages = Mockery::mock(Language::class),
			$multi_store = Mockery::mock(MultiStore::class)
		);

		$settings->shouldReceive('load')->with('static:application_token')->once()->andReturn('token');
		$order_status->shouldReceive('load')->withNoArgs()->once()->andReturn(['wc-pending' => 'wc-pending', 'wc-processing' => 'wc-processing']);
		$return_status->shouldReceive('load')->withNoArgs()->once()->andReturn(['wc-return-1' => 'wc-return-1', 'wc-return-2' => 'wc-return-2']);
		$languages->shouldReceive('load')->withNoArgs()->once()->andReturn(['cs' => 'cs', 'en' => 'en']);
		$multi_store->shouldReceive('load')->withNoArgs()->once()->andReturn(['default' => 'default', 'store1' => 'store1']);

		$settings->shouldReceive('load')->with(':order_status_list')->once()->andReturn([]);
		$settings->shouldReceive('set')->with(':order_status_list', ['wc-pending' => 'wc-pending', 'wc-processing' => 'wc-processing'], ['type' => 'array'])->once();
		$settings->shouldReceive('load')->with(':return_status_list')->once()->andReturn([]);
		$settings->shouldReceive('set')->with(':return_status_list', ['wc-return-1' => 'wc-return-1', 'wc-return-2' => 'wc-return-2'], ['type' => 'array'])->once();
		$settings->shouldReceive('load')->with(':languages')->once()->andReturn([]);
		$settings->shouldReceive('set')->with(':languages', ['cs' => 'cs', 'en' => 'en'], ['type' => 'array'])->once();
		$settings->shouldReceive('load')->with(':stores')->once()->andReturn([]);
		$settings->shouldReceive('set')->with(':stores', ['default' => 'default', 'store1' => 'store1'], ['type' => 'array'])->once();

		$synchronizer->shouldReceive('synchronize')->with(true)->once();

		$eshop_synchronizer->run();

		Assert::true(true);
	}


	public function testActual(): void
	{
		$eshop_synchronizer = new EshopSynchronizer(
			$synchronizer = Mockery::mock(Synchronizer::class),
			$settings = Mockery::mock(Settings::class),
			$order_status = Mockery::mock(OrderStatus::class),
			$return_status = Mockery::mock(ReturnStatus::class),
			$languages = Mockery::mock(Language::class),
			$multi_store = Mockery::mock(MultiStore::class)
		);

		$settings->shouldReceive('load')->with('static:application_token')->twice()->andReturn('token');
		$settings->shouldReceive('load')->with('static:application_token')->once()->andReturnNull();
		$order_status->shouldReceive('load')->withNoArgs()->twice()->andReturn(['wc-pending' => 'wc-pending', 'wc-processing' => 'wc-processing']);
		$return_status->shouldReceive('load')->withNoArgs()->twice()->andReturn(['wc-return-1' => 'wc-return-1', 'wc-return-2' => 'wc-return-2']);
		$languages->shouldReceive('load')->withNoArgs()->twice()->andReturn(['cs' => 'cs', 'en' => 'en']);
		$multi_store->shouldReceive('load')->withNoArgs()->twice()->andReturn(['default' => 'default', 'store1' => 'store1']);

		$settings->shouldReceive('load')->with(':order_status_list')->twice()->andReturn(['wc-pending' => 'wc-pending', 'wc-processing' => 'wc-processing']);
		$settings->shouldReceive('load')->with(':return_status_list')->twice()->andReturn(['wc-return-1' => 'wc-return-1', 'wc-return-2' => 'wc-return-2']);
		$settings->shouldReceive('load')->with(':languages')->twice()->andReturn(['cs' => 'cs', 'en' => 'en']);
		$settings->shouldReceive('load')->with(':stores')->twice()->andReturn(['default' => 'default', 'store1' => 'store1']);

		$synchronizer->shouldReceive('synchronize')->with(false)->once();
		$synchronizer->shouldReceive('synchronize')->with(true)->once();

		$eshop_synchronizer->run();
		$eshop_synchronizer->run(true);
		$eshop_synchronizer->run(true);

		Assert::true(true);
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new EshopSynchronizerTest())->run();
