<?php declare(strict_types=1);

namespace BulkGate\Plugin\Localization\Test;

require __DIR__ . '/../bootstrap.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{Localization\LanguageSettings, Settings\Settings};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class LanguageSettingsTest extends TestCase
{
	public function testGetFromSettings(): void
	{
		$language = new LanguageSettings('fr', $settings = Mockery::mock(Settings::class));
		$settings->shouldReceive('load')->with('main:language')->andReturn('sk');

		Assert::same('sk', $language->get());
	}


	public function testGetFromNullAuto(): void
	{
		$language = new LanguageSettings('fr', $settings = Mockery::mock(Settings::class));
		$settings->shouldReceive('load')->with('main:language')->andReturnNull();
		$settings->shouldReceive('set')->with('main:language', 'auto', ['type' => 'string'])->once();

		Assert::same('fr', $language->get());
	}


	public function testGetFromAuto(): void
	{
		$language = new LanguageSettings('cs', $settings = Mockery::mock(Settings::class));
		$settings->shouldReceive('load')->with('main:language')->andReturn('auto');

		Assert::same('cs', $language->get());
	}


	public function testGetFromDefault(): void
	{
		$language = new LanguageSettings(null, $settings = Mockery::mock(Settings::class));
		$settings->shouldReceive('load')->with('main:language')->andReturn('auto');

		Assert::same('en', $language->get());
	}
}

(new LanguageSettingsTest())->run();
