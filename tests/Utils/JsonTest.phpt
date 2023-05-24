<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\{JsonException, Utils\Json};

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class JsonTest extends TestCase
{
	public function testEncode(): void
	{
		Assert::same('[]', Json::encode([]));
		Assert::same('{"xxxx":true}', Json::encode((object)['xxxx' => true]));
		Assert::same('null', Json::encode(null));
		Assert::same('true', Json::encode(true));
		Assert::same('5', Json::encode(5));
		Assert::same('5.41', Json::encode(5.41));
		Assert::same('"1"', Json::encode('1'));
		Assert::same('""', Json::encode(''));
		Assert::exception(fn () => Json::encode("\xECQ"), JsonException::class);
	}


	public function testDecode(): void
	{
		Assert::same([], Json::decode('[]'));
		Assert::same(['xxxx' => true], Json::decode('{"xxxx":true}'));
		Assert::same(null, Json::decode('null'));
		Assert::same(true, Json::decode('true'));
		Assert::same(5, Json::decode('5'));
		Assert::same(5.41, Json::decode('5.41'));
		Assert::same('1', Json::decode('"1"'));
		Assert::same('', Json::decode('""'));
		Assert::exception(fn () => Json::decode("\xECQ"), JsonException::class);
	}
}

(new JsonTest())->run();
