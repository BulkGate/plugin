<?php declare(strict_types=1);

namespace BulkGate\Plugin\User\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Debug\Logger,
	Eshop\ConfigurationDefault,
	IO\Connection,
	IO\Url,
	Localization\Language,
	Settings\Settings,
	User\Sign,
	Utils\Jwt};

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SignTest extends TestCase
{
	public function testDefaultParameters(): void
	{
		$sign = new Sign($settings = Mockery::mock(Settings::class), Mockery::mock(Connection::class), new Url(), new ConfigurationDefault('url', 'eshop', '1.0', 'Test Eshop'), $language = Mockery::mock(Language::class), Mockery::mock(Logger::class));
		$sign->setDefaultParameters(['test1' => 'default_test1', 'test3' => 'default_test3']);

		$settings->shouldReceive('load')->with('static:application_token', true)->andReturn('test_application_token');
		$settings->shouldReceive('load')->with('static:application_id')->andReturn(12345);
		$language->shouldReceive('get')->withNoArgs()->once()->andReturn('cs');

		$jwt = Mockery::mock('overload:' . Jwt::class);
		$jwt->shouldReceive('encode')->with([
			'application_id' => 12345,
			'application_installation' => 'url',
			'application_product' => 'eshop',
			'application_language' => 'cs',
			'application_version' => '1.0',
			'application_parameters' => [
				'guest' => false,
				'test1' => 'test1',
				'test2' => 'test2',
				'test3' => 'default_test3'
			]
		], 'test_application_token')->once()->andReturn('0.0.0');


		Assert::match('~^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+$~', $sign->authenticate(true, ['test1' => 'test1', 'test2' => 'test2']));
	}

	public function testGuestParameterOverride(): void
	{
		$sign = new Sign($settings = Mockery::mock(Settings::class), Mockery::mock(Connection::class), new Url(), new ConfigurationDefault('url', 'eshop', '1.0', 'Test Eshop'), $language = Mockery::mock(Language::class), Mockery::mock(Logger::class));

		$settings->shouldReceive('load')->with('static:application_token', true)->andReturn('test_application_token');
		$settings->shouldReceive('load')->with('static:application_id')->andReturn(12345);
		$language->shouldReceive('get')->withNoArgs()->once()->andReturn('cs');

		$jwt = Mockery::mock('overload:' . Jwt::class);
		$jwt->shouldReceive('encode')->with([
			'application_id' => 12345,
			'application_installation' => 'url',
			'application_product' => 'eshop',
			'application_language' => 'cs',
			'application_version' => '1.0',
			'application_parameters' => [
				'guest' => false,
			]
		], 'test_application_token')->once()->andReturn('0.0.0');

		Assert::match('~^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+$~', $sign->authenticate(true, ['guest' => 'someValue']));
	}

	public function testGuestParameterNullToken(): void
	{
		$sign = new Sign($settings = Mockery::mock(Settings::class), Mockery::mock(Connection::class), new Url(), new ConfigurationDefault('url', 'eshop', '1.0', 'Test Eshop'), $language = Mockery::mock(Language::class), Mockery::mock(Logger::class));

		$settings->shouldReceive('load')->with('static:application_token', false)->andReturn(null);
		$settings->shouldReceive('load')->with('static:application_id')->andReturn(12345);
		$language->shouldReceive('get')->withNoArgs()->once()->andReturn('cs');

		$jwt = Mockery::mock('overload:' . Jwt::class);
		$jwt->shouldReceive('encode')->with([
			'application_id' => 12345,
			'application_installation' => 'url',
			'application_product' => 'eshop',
			'application_language' => 'cs',
			'application_version' => '1.0',
			'application_parameters' => [
				'guest' => true,
			]
		], '')->once()->andReturn('0.0.0');

		Assert::match('~^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+$~', $sign->authenticate());
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new SignTest())->run();
