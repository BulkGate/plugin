<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\Debug\Requirements;
use Tester\{Assert, TestCase};

require __DIR__ . '/../bootstrap.php';

/**
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
				'error' => null,
			],
			[
				'passed' => false,
				'description' => 'test fail optional',
				'color' => 'darkorange',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'PHP >= 7.4 version',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'INTL extension',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'cURL extension',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'Multibyte string extension',
				'color' => 'limegreen',
				'error' => null,
			],
			[
				'passed' => true,
				'description' => 'Compression extension',
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
