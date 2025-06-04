<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Database\Result;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class ResultTest extends TestCase
{
	public function testArrayAccessAndCountAndToArray(): void
	{
		$collection = new Result(['test' => 'ok']);

		Assert::same('ok', $collection->test);

		$collection->test = 'ok2';

		Assert::same('ok2', $collection->test);
	}
}

(new ResultTest())->run();
