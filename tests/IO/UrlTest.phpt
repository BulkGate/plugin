<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO\Test;

require __DIR__ . '/../bootstrap.php';


use Tester\{Assert, TestCase};
use BulkGate\Plugin\IO\Url;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class UrlTest extends TestCase
{
	public function testBase(): void
	{
		Assert::same('https://portal.bulkgate.com/api/welcome', (new Url())->get('api/welcome'));
		Assert::same('https://portal.bulkgate.com/api/test/test', (new Url('https://portal.bulkgate.com'))->get('api/test/test'));
		Assert::same('https://xxx.bulkgate.com/api/welcome', (new Url('https://xxx.bulkgate.com'))->get('api/welcome'));
	}
}

(new UrlTest())->run();
