<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

require __DIR__ . '/../bootstrap.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\Event\{DataLoader, Loader, Variables};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class LoaderTest extends TestCase
{
	public function testLoad(): void
	{
		$variables = new Variables();

		$loader = new Loader([
			$l1 = Mockery::mock(DataLoader::class),
			$l2 = Mockery::mock(DataLoader::class),
			$l3 = Mockery::mock(DataLoader::class)
		]);
		$l1->shouldReceive('load')->with($variables, ['ok'])->once();
		$l2->shouldReceive('load')->with($variables, ['ok'])->once();
		$l3->shouldReceive('load')->with($variables, ['ok'])->once();

		$loader->load($variables, ['ok']);

		Assert::true(true);

		Mockery::close();
	}
}

(new LoaderTest())->run();
