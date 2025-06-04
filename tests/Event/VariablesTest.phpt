<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Event\Variables;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
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


	public function testDeprecated(): void
	{
		Assert::type(Variables::class, new Variables());

		$v1 = new \BulkGate\Extensions\Hook\Variables([]);

		$v1->set('test1', 'ok');

		Assert::same(['test1' => 'ok'], $v1->toArray());

		$v1->set('test1', 'fail', '', false);
		$v1->set('test2', 'ok', '', false);
		$v1->set('test3', null, 'test', false);
		Assert::same($v1, $v1->set('test4', null, null, false));

		Assert::same(['test1' => 'ok', 'test2' => 'ok', 'test3' => 'test', 'test4' => ''], $v1->toArray());

		(function (\BulkGate\Extensions\Hook\Variables $variables): void
		{
			$variables->set('test2', 'ok');
		})($v2 = new Variables());

		Assert::same(['test2' => 'ok'], $v2->toArray());

		Assert::null($v2->get('test1'));
		Assert::same('ok', $v2->get('test2'));
		Assert::same('ok-default', $v2->get('test3', 'ok-default'));
	}
}

(new VariablesTest())->run();
