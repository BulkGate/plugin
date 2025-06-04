<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO\Test;

require __DIR__ . '/../bootstrap.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{InvalidJwtException, IO\Connection, IO\ConnectionCurl, IO\ConnectionFactory, IO\Request, Settings\Settings};
use const NAN;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class ConnectionFactoryTest extends TestCase
{
	public function testCreate(): void
	{
		$factory = new ConnectionFactory('https://www.insymbo.com/', 'insymbo', $settings = Mockery::mock(Settings::class));
		$settings->shouldNotReceive('load')->with('static:application_id')->once()->andReturn(451);
		$settings->shouldNotReceive('load')->with('static:language')->once()->andReturn('no');
		$settings->shouldNotReceive('load')->with('static:application_token')->once()->andReturn('secret');

		$connection = $factory->create();

		Assert::type(Connection::class, $connection);
		Assert::type(ConnectionCurl::class, $connection);

		Assert::with($connection, function (): void
		{
			Assert::same('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcHBsaWNhdGlvbl9pZCI6NDUxLCJhcHBsaWNhdGlvbl91cmwiOiJodHRwczovL3d3dy5pbnN5bWJvLmNvbS8iLCJhcHBsaWNhdGlvbl9wcm9kdWN0IjoiaW5zeW1ibyIsImFwcGxpY2F0aW9uX2xhbmd1YWdlIjoibm8ifQ.kMxlSo5ii4x258k8UY5e7U-bXHEE4G-4l0_s0QYhQ-8', ($this->jwt_factory)());
		});
	}


	public function testInvalid(): void
	{
		$factory = new ConnectionFactory('https://www.insymbo.com/', 'insymbo', $settings = Mockery::mock(Settings::class));
		$settings->shouldNotReceive('load')->with('static:application_id')->once()->andReturn(451);
		$settings->shouldNotReceive('load')->with('static:language')->once()->andReturn(NAN);
		$settings->shouldNotReceive('load')->with('static:application_token')->once()->andReturn('secret');

		Assert::exception(fn() => $factory->create()->run(new Request('url')), InvalidJwtException::class, 'Unable to create JWT');
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new ConnectionFactoryTest())->run();
