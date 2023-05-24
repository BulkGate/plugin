<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Event\Variables;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class VariablesTest extends TestCase
{
	public function testBase(): void
	{
		$variables = new Variables();

		Assert::false(isset($variables['test']));

		$variables['test'] = 'test';

		Assert::true(isset($variables['test']));

		Assert::same('test', $variables['test']);

		Assert::same(['test' => 'test'], $variables->toArray());

		Assert::count(1, $variables);

		unset($variables['test']);

		Assert::false(isset($variables['test']));

		Assert::count(0, $variables);

		$variables['test1'] = 'test1';
		$variables['test2'] = 'test2';
		$variables['test3'] = 'test3';
		$variables['test2'] = 'test4';

		$values = [];

		foreach ($variables as $key => $value)
		{
			$values[$key] = $value;
		}

		Assert::same(['test1' => 'test1', 'test2' => 'test4', 'test3' => 'test3'], $values);
	}


	public function testSet(): void
	{
		$variables = new Variables();

		$variables['test1'] = 'test';
		$variables['test1'] = '';
		$variables['test1'] = '    ';

		$variables['test2'] = '';
		$variables['test2'] = null;

		$variables['test3'] = '';
		$variables['test3'] = null;
		$variables['test3'] = 'ok';

		$variables['test4'] = null;
		$variables['test4'] = '';

		$variables['test5'] = null;
		$variables['test5'] = '';
		$variables['test5'] = 'ok';

		Assert::same(['test1' => 'test', 'test2' => '', 'test3' => 'ok', 'test4' => null, 'test5' => 'ok'], $variables->toArray());
	}
}

(new VariablesTest())->run();
