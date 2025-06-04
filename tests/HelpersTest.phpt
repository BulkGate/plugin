<?php declare(strict_types=1);

namespace BulkGate\Plugin\Test;

require __DIR__ . '/bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Helpers;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class HelpersTest extends TestCase
{
	public function testPath(): void
	{
		Assert::same(['_generic', 'server', '_empty'], Helpers::path(''));
		Assert::same(['_generic', 'server', '_empty'], Helpers::path(':'));
		Assert::same(['_generic', 'server', '_empty'], Helpers::path('::'));
		Assert::same(['reducer', 'server', '_empty'], Helpers::path('reducer::'));
		Assert::same(['reducer', 'scope', '_empty'], Helpers::path('reducer:scope:'));
		Assert::same(['reducer', 'scope', 'variable'], Helpers::path('reducer:scope:variable'));
		Assert::same(['reducer', 'server', 'variable'], Helpers::path('reducer::variable'));
		Assert::same(['_generic', 'scope', '_empty'], Helpers::path(':scope:'));
		Assert::same(['_generic', 'scope', 'variable'], Helpers::path(':scope:variable'));
		Assert::same(['_generic', 'server', 'variable'], Helpers::path('::variable'));
		Assert::same(['_generic', 'server', 'variable'], Helpers::path('variable'));
		Assert::same(['_generic', 'server', 'variable'], Helpers::path(':variable'));
		Assert::same(['_generic', 'scope', 'variable'], Helpers::path('scope:variable'));
		Assert::same(['_generic', 'scope', '_empty'], Helpers::path('scope:'));
	}


	public function testReduceStructure(): void
	{
		$structure = [
			'_generic' => [
				'scope' => [
					'test' => 'BulkGate'
				],
				'server' => [
					'user' => null,
					'admin' => 'Lukáš'
				]
			],
			'reducer' => [
				'server' => [
					'admin' => 'Marek'
				]
			]
		];

		Assert::same('BulkGate', Helpers::reduceStructure($structure, ':scope:test'));
		Assert::same(['test' => 'BulkGate'], Helpers::reduceStructure($structure, ':scope:'));
		Assert::same(['user' => null, 'admin' => 'Lukáš'], Helpers::reduceStructure($structure, '::'));
		Assert::null(Helpers::reduceStructure($structure, '::user'));
		Assert::same('Lukáš', Helpers::reduceStructure($structure, '::admin'));
		Assert::same('Lukáš', Helpers::reduceStructure($structure, ':admin'));
		Assert::same('BulkGate', Helpers::reduceStructure($structure, 'scope:test'));
		Assert::same('Lukáš', Helpers::reduceStructure($structure, 'admin'));
		Assert::same('Marek', Helpers::reduceStructure($structure, 'reducer::admin'));
		Assert::null(Helpers::reduceStructure($structure, 'reducer::user'));
		Assert::same(['admin' => 'Marek'], Helpers::reduceStructure($structure, 'reducer::'));
	}
}

(new HelpersTest())->run();
