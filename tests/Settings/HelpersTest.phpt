<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Settings\Helpers;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class HelpersTest extends TestCase
{
	public function testKey(): void
	{
		Assert::same(['scope', 'test'], Helpers::key('scope:test'));
		Assert::same(['main', 'test'], Helpers::key(':test'));
		Assert::same(['main', 'test'], Helpers::key('test'));
		Assert::same(['scope', null], Helpers::key('scope:'));
		Assert::same(['main', null], Helpers::key(''));

		Assert::same(['test-test_sss', '_-test'], Helpers::key('test-test_sss:_-test'));
		Assert::same(['sco_pe', 'te-st'], Helpers::key('sco_pe:te-st'));

		Assert::same(['s_cope', 'test_sss'], Helpers::key('s_cope:test_sss'));
		Assert::same(['main', 'test_sss'], Helpers::key('test_sss'));
		Assert::same(['main', null], Helpers::key('test_sss : : test'));
	}


	public function testCheckEnum(): void
	{
		Assert::same('test', Helpers::checkEnum('test', ['test', 'invalid'], 'xxx'));
		Assert::same('invalid', Helpers::checkEnum('invalid', ['test', 'invalid'], 'xxx'));
		Assert::same('xxx', Helpers::checkEnum('invalid', [], 'xxx'));
	}


	public function testDetectType(): void
	{
		Assert::same('bool', Helpers::detectType(false));
		Assert::same('int', Helpers::detectType(0));
		Assert::same('float', Helpers::detectType(0.0));
		Assert::same('array', Helpers::detectType([]));
		Assert::same('string', Helpers::detectType(''));
		Assert::same('string', Helpers::detectType('   '));

		Assert::same('bool', Helpers::detectType(true));
		Assert::same('int', Helpers::detectType(1));
		Assert::same('float', Helpers::detectType(1.0));
		Assert::same('array', Helpers::detectType(['xxx' => []]));
		Assert::same('string', Helpers::detectType('test'));
		Assert::null(Helpers::detectType((object)[]));
	}


	public function testSerializeValue(): void
	{
		Assert::same('0', Helpers::serializeValue(false));
		Assert::same('0', Helpers::serializeValue(0));
		Assert::same('0', Helpers::serializeValue(0.0));
		Assert::same('[]', Helpers::serializeValue([]));
		Assert::same('', Helpers::serializeValue(''));
		Assert::same('   ', Helpers::serializeValue('   '));

		Assert::same('1', Helpers::serializeValue(true));
		Assert::same('1', Helpers::serializeValue(1));
		Assert::same('1', Helpers::serializeValue(1.0));
		Assert::same('1.1', Helpers::serializeValue(1.1));
		Assert::same('{"xxx":[]}', Helpers::serializeValue(['xxx' => []]));
		Assert::same('[]', Helpers::serializeValue(['xxx' => ["\xEC"]]));
		Assert::same('test', Helpers::serializeValue('test'));
		Assert::same('', Helpers::serializeValue((object)[]));
	}


	public function testDeserializeValue(): void
	{
		Assert::false(Helpers::deserializeValue('0', 'bool'));
		Assert::true(Helpers::deserializeValue('1', 'bool'));
		Assert::same(0, Helpers::deserializeValue('0', 'int'));
		Assert::same(1, Helpers::deserializeValue('1', 'int'));
		Assert::same('0', Helpers::deserializeValue('0', 'string'));
		Assert::same('1', Helpers::deserializeValue('1', 'string'));
		Assert::same('0', Helpers::deserializeValue('0', 'text'));
		Assert::same('1', Helpers::deserializeValue('1', 'text'));
		Assert::same(0.0, Helpers::deserializeValue('0', 'float'));
		Assert::same(1.0, Helpers::deserializeValue('1', 'float'));
		Assert::same([], Helpers::deserializeValue('[]', 'array'));
		Assert::same([], Helpers::deserializeValue('[]', 'json'));

		Assert::same(['test'], Helpers::deserializeValue('["test"]', 'array'));
		Assert::same(['test' => 'ok'], Helpers::deserializeValue('{"test":"ok"}', 'array'));

		Assert::null(Helpers::deserializeValue('[]', 'invalid_type'));
		Assert::same([], Helpers::deserializeValue(']', 'array'));
	}
}

(new HelpersTest())->run();
