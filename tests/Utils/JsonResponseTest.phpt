<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\{JsonException, Utils\JsonArray, Utils\JsonResponse};

require __DIR__ . '/../bootstrap.php';

/**
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
