<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Debug\Requirements;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class RequirementsTest extends TestCase
{
	public function testRun(): void
	{
		$requirements = new Requirements();

		Assert::same([
			[
				'passed' => false,
				'description' => 'test fail required',
				'color' => 'red',
				'error' => 'true !== false',
			],
			[
				'passed' => false,
				'description' => 'test fail optional',
				'color' => 'darkorange',
				'error' => 'true !== false',
			],
			[
				'passed' => true,
				'description' => 'PHP >= 7.4 Version',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'INTL Extension',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'cURL Extension',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'Multibyte String Extension',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'Compression Extension',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'Serialize',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'Base64',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'JSON',
				'color' => 'limegreen',
				'error' => null,
			],
		], $requirements->run([
			$requirements->same(false, true, 'test fail required'),
			$requirements->same(false, true, 'test fail optional', true)
		]));

		Assert::same(10, $requirements->count);
	}
}

(new RequirementsTest())->run();
