<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Utils\Strings;
use function mb_strlen;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class StringsTest extends TestCase
{
	public function testLength(): void
	{
		Assert::same(5, Strings::length('tests'));
		Assert::same(0, Strings::length(''));
		Assert::true(Strings::length("\xEC") > 0);
	}


	public function testUpper(): void
	{
		Assert::same('TESTS', Strings::upper('tEsTs'));
		Assert::same('', Strings::upper(''));

		Assert::same('TESTS', Strings::upper('tEsTs', 'ASCII'));
		Assert::same('', Strings::upper('', 'ASCII'));
	}


	public function testLower(): void
	{
		Assert::same('tests', Strings::lower('tEsTs'));
		Assert::same('', Strings::lower(''));

		Assert::same('tests', Strings::lower('tEsTs', 'ASCII'));
		Assert::same('', Strings::lower('', 'ASCII'));
	}


	public function testStrlenPolyfill(): void
	{
		Assert::same(mb_strlen('Hello World'), Strings::strlenPolyfill('Hello World'));
		Assert::same(mb_strlen('Příliš žluťoučký kůň'), Strings::strlenPolyfill('Příliš žluťoučký kůň'));
		Assert::same(mb_strlen('我'), Strings::strlenPolyfill('我'));
		Assert::same(mb_strlen('😀👍'), Strings::strlenPolyfill('😀👍'));
		Assert::same(mb_strlen('€'), Strings::strlenPolyfill('€'));
		Assert::same(mb_strlen(''), Strings::strlenPolyfill(''));
		Assert::same(mb_strlen("\xFB"), Strings::strlenPolyfill("\xFB"));
		Assert::same(mb_strlen('🇦🇺'), Strings::strlenPolyfill("🇦🇺"));
		Assert::same(mb_strlen("\xFD\xFD\xFD\xFD\xFD\xFD"), Strings::strlenPolyfill("\xFD\xFD\xFD\xFD\xFD\xFD"));
	}


	public function testStrToLowerPolyfill(): void
	{
		Assert::same('hello world', Strings::strToLowerPolyfill('Hello World'));
		//Assert::same('příliš žluťoučký kůň', Strings::strToLowerPolyfill('Příliš žluťoučký kůň'));
		Assert::same('我', Strings::strToLowerPolyfill('我'));
		//Assert::same('😀👍', Strings::strToLowerPolyfill('😀👍'));
		Assert::same('€', Strings::strToLowerPolyfill('€'));
		Assert::same('', Strings::strToLowerPolyfill(''));
		Assert::same('', Strings::strToLowerPolyfill("\xFB"));
		Assert::same('🇦🇺', Strings::strToLowerPolyfill("🇦🇺"));
		Assert::same('', Strings::strToLowerPolyfill("\xFD\xFD\xFD\xFD\xFD\xFD"));
	}


	public function testStrToUpperPolyfill(): void
	{
		Assert::same('HELLO WORLD', Strings::strToUpperPolyfill('Hello World'));
		//Assert::same('PříLIš žLUťOUčKý Kůň', Strings::strToUpperPolyfill('Příliš žluťoučký kůň'));
		//Assert::same('我', Strings::strToUpperPolyfill('我'));
		//Assert::same('😀👍', Strings::strToUpperPolyfill('😀👍'));
		//Assert::same('€', Strings::strToUpperPolyfill('€'));
		Assert::same('', Strings::strToUpperPolyfill(''));
		Assert::same('', Strings::strToUpperPolyfill("\xFB"));
		//Assert::same('🇦🇺', Strings::strToUpperPolyfill("🇦🇺"));
		Assert::same('', Strings::strToUpperPolyfill("\xFD\xFD\xFD\xFD\xFD\xFD"));
	}
}

(new StringsTest())->run();
