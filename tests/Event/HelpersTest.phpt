<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event\Test;

require_once __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Event\Helpers;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class HelpersTest extends TestCase
{
	public function testAddress(): void
	{
		Assert::same('1', Helpers::address('key', ['key' => '1', 'invalid' => false], ['key' => '2', 'invalid' => false]));
		Assert::same('2', Helpers::address('key', ['invalid' => false], ['key' => '2', 'invalid' => false]));
		Assert::null(Helpers::address('key', ['invalid' => false], ['invalid' => false]));
	}


	public function testJoinStreet(): void
	{
		Assert::same('p1, p2', Helpers::joinStreet('k1', 'k2', ['k1' => 'p1', 'k2' => 'p2', 'invalid' => '0'], ['k1' => 's1', 'k2' => 's2', 'invalid' => '0']));
		Assert::same('p1', Helpers::joinStreet('k1', 'k2', ['k1' => 'p1', 'invalid' => '0'], ['k1' => 's1', 'k2' => 's2', 'invalid' => '0']));
		Assert::same('p1, p2', Helpers::joinStreet('k1', 'k2', ['k1' => 'p1', 'k2' => 'p2', 'invalid' => '0'], ['k2' => 's2', 'invalid' => '0']));
		Assert::same('s1', Helpers::joinStreet('k1', 'k2', ['k2' => 'p2', 'invalid' => '0'], ['k1' => 's1', 'invalid' => '0']));
		Assert::same('s1, s2', Helpers::joinStreet('k1', 'k2', ['k2' => 'p2', 'invalid' => '0'], ['k1' => 's1', 'k2' => 's2', 'invalid' => '0']));
		Assert::same('s1, s2', Helpers::joinStreet('k1', 'k2', ['invalid' => '0'], ['k1' => 's1', 'k2' => 's2', 'invalid' => '0']));

		Assert::null(Helpers::joinStreet('k1', 'k2', ['invalid' => '0'], ['invalid' => '0']));
	}
}

(new HelpersTest())->run();
