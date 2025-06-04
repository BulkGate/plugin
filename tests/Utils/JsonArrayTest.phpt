<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Utils\JsonArray;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class JsonArrayTest extends TestCase
{
	public function testEncode(): void
	{
		Assert::same('[]', JsonArray::encode([]));
		Assert::same('{"xxxx":true}', JsonArray::encode(['xxxx' => true]));
		Assert::same('[]', JsonArray::encode(["\xECQ"]));
	}


	public function testDecode(): void
	{
		Assert::same([], JsonArray::decode('[]'));
		Assert::same(['xxxx' => true], JsonArray::decode('{"xxxx":true}'));
		Assert::same([], JsonArray::decode('null'));
		Assert::same([], JsonArray::decode('true'));
		Assert::same([], JsonArray::decode('5'));
		Assert::same([], JsonArray::decode('5.41'));
		Assert::same([], JsonArray::decode('"1"'));
		Assert::same([], JsonArray::decode('""'));
		Assert::same([], JsonArray::decode("\xECQ"));
	}
}

(new JsonArrayTest())->run();
