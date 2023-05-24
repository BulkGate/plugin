<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\IO\Request;
use const NAN;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class RequestTest extends TestCase
{
	public function testRequest(): void
	{
		$request = new Request('https://portal.bulkgate.com/', ['test' => 'test'], 'application/json', 30);

		Assert::same('https://portal.bulkgate.com/', $request->url);
		Assert::same('application/json', $request->content_type);
		Assert::same(['test' => 'test'], $request->data);
		Assert::same(30, $request->timeout);

		Assert::same('{"test":"test"}', $request->serialize());
	}


	public function testRequestInvalid(): void
	{
		$request = new Request('https://portal.bulkgate.com/', ['test' => NAN], 'application/zip');

		Assert::same('https://portal.bulkgate.com/', $request->url);
		Assert::same('application/zip', $request->content_type);
		Assert::same(20, $request->timeout);

		Assert::same('[]', $request->serialize());
	}
}

(new RequestTest())->run();
