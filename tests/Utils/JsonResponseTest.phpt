<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Utils\JsonResponse;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class JsonResponseTest extends TestCase
{
	public function testResponse(): void
	{
		Assert::true(true);
		JsonResponse::send(['test']);
	}
}

(new JsonResponseTest())->run();
