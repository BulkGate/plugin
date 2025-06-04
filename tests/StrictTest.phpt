<?php declare(strict_types=1);

namespace BulkGate\Plugin\Test;

require __DIR__ . '/bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Strict, StrictException};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class StrictTest extends TestCase
{
	public function testStrict(): void
	{
		$class = new class ()
		{
			use Strict;
		};

		Assert::false(isset($class->test));
		Assert::exception(fn() => $class->test, StrictException::class);
		Assert::exception(function () use ($class): void {
			unset($class->test);
		}, StrictException::class);
		Assert::exception(fn() => $class->test = 'test', StrictException::class);
		Assert::exception(fn() => $class->test(), StrictException::class);
		Assert::exception(fn() => $class::test(), StrictException::class);
	}
}

(new StrictTest())->run();
