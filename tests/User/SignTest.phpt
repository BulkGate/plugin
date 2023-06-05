<?php declare(strict_types=1);

namespace BulkGate\Plugin\User\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, Expect, TestCase};
use BulkGate\Plugin\{Eshop\ConfigurationDefault, InvalidResponseException, IO\Connection, IO\Request, IO\Response, IO\Url, Settings\Settings, User\Sign};
use function json_encode;

require_once __DIR__ . '/../bootstrap.php';

class SignTest extends TestCase
{
	public function testAuthenticate(): void
	{
		$sign = new Sign($settings = Mockery::mock(Settings::class), Mockery::mock(Connection::class), new Url(), new ConfigurationDefault('url', 'eshop', '1.0', 'Test Eshop'));

		$settings->shouldReceive('load')->with('static:application_token', false)->once()->andReturn('test_application_token');
		$settings->shouldReceive('load')->with('static:application_id')->once()->andReturn(12345);
		$settings->shouldReceive('load')->with('main:language')->once()->andReturn('cs');

		Assert::match('~^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+$~', $sign->authenticate());
	}


	public function testIn(): void
	{
		$sign = new Sign($settings = Mockery::mock(Settings::class), $connection = Mockery::mock(Connection::class), new Url(), new ConfigurationDefault('url', 'eshop', '1.0', 'Test Eshop'));
		$settings->shouldReceive('install')->withNoArgs()->once();
		$connection->shouldReceive('run')->with(Mockery::on(function (Request $request): bool
		{
			Assert::same('{"email":"test@example.com","password":"test_password","name":"Test Eshop"}', $request->serialize());

			return true;
		}))->andReturn(new Response(json_encode(['data' => [
			'application_id' => 12345,
			'application_token' => 'test_application_token',
		]])));
		$settings->shouldReceive('set')->with('static:application_token', 'test_application_token', ['type' => 'string']);
		$settings->shouldReceive('set')->with('static:application_id', 12345, ['type' => 'int']);
		$settings->shouldReceive('load')->with('main:language')->once()->andReturn('cs');
		$settings->shouldReceive('set')->with('static:synchronize', 0, ['type' => 'int']);

		$settings->shouldReceive('load')->with('static:application_token', true)->andReturn('test_application_token');
		$settings->shouldReceive('load')->with('static:application_id')->andReturn(12345);

		$result = $sign->in('test@example.com', 'test_password', 'test_success_redirect');

		Assert::equal(['token' => Expect::match('~^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+$~'), 'redirect' => 'test_success_redirect'], $result);

		Assert::type('array', $result);
		Assert::true(isset($result['token']));
		Assert::type('string', $result['token']);
		Assert::same('test_success_redirect', $result['redirect']);
	}


	public function testInInvalid(): void
	{
		$sign = new Sign(Mockery::mock(Settings::class), $connection = Mockery::mock(Connection::class), new Url(), new ConfigurationDefault('url', 'eshop', '1.0', 'Test Eshop'));
		$connection->shouldReceive('run')->with(Mockery::on(function (Request $request): bool {
			Assert::same('{"email":"test@example.com","password":"test_password","name":"Test Eshop"}', $request->serialize());

			return true;
		}))->andReturn(new Response(json_encode(['data' => ['_generic' => ['server' => ['login' => []]]]])));

		Assert::same(['error' => ['unknown_error']], $sign->in('test@example.com', 'test_password', 'test_success_redirect'));
	}


	public function testInError(): void
	{
		$sign = new Sign(Mockery::mock(Settings::class), $connection = Mockery::mock(Connection::class), new Url(), new ConfigurationDefault('url', 'eshop', '1.0', 'Test Eshop'));
		$connection->shouldReceive('run')->with(Mockery::on(function (Request $request): bool {
			Assert::same('{"email":"test@example.com","password":"test_password","name":"Test Eshop"}', $request->serialize());

			return true;
		}))->andThrow(InvalidResponseException::class, 'test_error');

		Assert::same(['error' => ['test_error']], $sign->in('test@example.com', 'test_password', 'test_success_redirect'));
	}


	public function testOut(): void
	{
		$sign = new Sign($setting = Mockery::mock(Settings::class), Mockery::mock(Connection::class), new Url(), new ConfigurationDefault('url', 'eshop', '1.0', 'Test Eshop'));
		$setting->shouldReceive('delete')->with('static:application_token')->andReturnNull();

		$sign->out();

		Assert::true(true);
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new SignTest())->run();
