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
		Assert::same('["اللُّغَة الْعَرَبِيَّة"]', Json::encode(['اللُّغَة الْعَرَبِيَّة']));
		Assert::same('[" \u0627\u0644\u0644\u064f\u0651\u063a\u064e\u0629 \u0627\u0644\u0652\u0639\u064e\u0631\u064e\u0628\u0650\u064a\u064e\u0651\u0629\u200e"]', Json::encode([' اللُّغَة الْعَرَبِيَّة‎'], false, true));
		Assert::same('[" اللُّغَة الْعَرَبِيَّة‎"]', Json::encode([' اللُّغَة الْعَرَبِيَّة‎']));
		//Assert::same([], Json::encode("\xECQ", false, true));
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
		Assert::same(['اللُّغَة الْعَرَبِيَّة'], Json::decode('["اللُّغَة الْعَرَبِيَّة"]'));
		Assert::same(['اللُّغَة الْعَرَبِيَّة'], Json::decode('["\u0627\u0644\u0644\u064f\u0651\u063a\u064e\u0629 \u0627\u0644\u0652\u0639\u064e\u0631\u064e\u0628\u0650\u064a\u064e\u0651\u0629"]'));
		Assert::same([' اللُّغَة الْعَرَبِيَّة‎'], Json::decode('[" اللُّغَة الْعَرَبِيَّة‎"]'));
	}
}

(new JsonTest())->run();
