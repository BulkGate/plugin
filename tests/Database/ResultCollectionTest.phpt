<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Database\ResultCollection;
use BulkGate\Plugin\Settings\Repository\Entity\Setting;

require __DIR__ . '/../bootstrap.php';

/**
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

		Assert::same($a, $collection[0]);
		Assert::same($b, $collection[1]);
		Assert::same($c, $collection['test']);

		Assert::count(3, $collection);
		unset($collection[1]);
		Assert::false(isset($collection[1]));
		Assert::count(2, $collection);
		Assert::null($collection[1]);

		Assert::same([$a, 'test' => $c], $collection->toArray());

		Assert::same(2, $collection->getNumRows());
		Assert::type(\stdClass::class, $collection->getRow());
		Assert::same(['test' => 1], (array) $collection->getRow());

		Assert::equal([
			(object) ['test' => 1],
			(object) ['test' => 3],
		], $collection->getRows());
	}
}

(new ResultCollectionTest())->run();
