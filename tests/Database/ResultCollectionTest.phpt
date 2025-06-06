<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Database\Result;
use BulkGate\Plugin\Database\ResultCollection;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class ResultCollectionTest extends TestCase
{
	public function testArrayAccessAndCountAndToArray(): void
	{
		$collection = new ResultCollection([$a = ['test' => 1]]);

		Assert::true(isset($collection[0]));
		Assert::false(isset($collection[1]));
		Assert::false(isset($collection['test']));

		$collection[] = $b = ['test' => 1];
		Assert::true(isset($collection[1]));
		$collection['test'] = $c = ['test' => 3];
		Assert::true(isset($collection['test']));

		Assert::same($a, $collection[0]->toArray());
		Assert::same($b, $collection[1]->toArray());
		Assert::same($c, $collection['test']->toArray());

		Assert::count(3, $collection);
		unset($collection[1]);
		Assert::false(isset($collection[1]));
		Assert::count(2, $collection);
		Assert::null($collection[1]);

		Assert::same([$collection[0], 'test' => $collection['test']], $collection->toArray());

		Assert::same(2, $collection->getNumRows());
		Assert::type(Result::class, $collection->getRow());
		Assert::same(['test' => 1], $collection->getRow()->toArray());

		Assert::count(2, $collection->getRows());
	}
}

(new ResultCollectionTest())->run();
