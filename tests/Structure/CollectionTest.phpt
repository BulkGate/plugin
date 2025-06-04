<?php declare(strict_types=1);

namespace BulkGate\Plugin\Database\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Structure\Collection, Settings\Repository\Entity\Setting};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class CollectionTest extends TestCase
{
	public function testArrayAccessAndCountAndToArray(): void
	{
		$collection = new Collection(Setting::class, [$a = new Setting()]);

		Assert::true(isset($collection[0]));
		Assert::false(isset($collection[1]));
		Assert::false(isset($collection['test']));

		$collection[] = $b = new Setting();
		Assert::true(isset($collection[1]));
		$collection['test'] = $c = new Setting();
		Assert::true(isset($collection['test']));

		Assert::same($a, $collection[0]);
		Assert::same($b, $collection[1]);
		Assert::same($c, $collection['test']);

		Assert::count(3, $collection);
		unset($collection[1]);
		Assert::false(isset($collection[1]));
		Assert::count(2, $collection);
		Assert::null($collection[1]);

		Assert::same([$a, 'test' => $c], $collection->toArray());
	}


	public function testIterator(): void
	{
		$count = 0;

		$a = new Setting();

		$collection = new Collection(Setting::class, [$a, $a, $a]);

		foreach ($collection as $item) {
			$count++;

			Assert::same($a, $item);
		}

		Assert::same(3, $count);
	}
}

(new CollectionTest())->run();
