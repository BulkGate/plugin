<?php declare(strict_types=1);

namespace BulkGate\Plugin\Localization\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use Mockery;
use BulkGate\Plugin\{Localization\Language, Settings\Repository\Entity\Setting, Settings\Settings, Localization\TranslatorSettings};
use function PHPStan\dumpType;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class TranslatorSettingsTest extends TestCase
{
	public function testBase(): void
	{
		$translator = new TranslatorSettings($settings = Mockery::mock(Settings::class), $language = Mockery::mock(Language::class));
		$language->shouldReceive('get')->withNoArgs()->once()->andReturn('cs');
		$settings->shouldReceive('load')->with('translates:cs')->once()->andReturn(['name' => 'jméno', 'age' => 'věk']);
		$language->shouldReceive('set')->with('fr')->once();
		$language->shouldReceive('get')->withNoArgs()->once()->andReturn('fr');
		$settings->shouldReceive('load')->with('translates:fr')->once()->andReturn(['name' => 'nom', 'age' => 'âge']);
		$language->shouldReceive('set')->with('sk')->once();
		$language->shouldReceive('get')->withNoArgs()->once()->andReturn('sk');
		$settings->shouldReceive('load')->with('translates:sk')->once()->andReturn('translates');

		Assert::same('jméno', $translator->translate('name'));
		Assert::same('věk', $translator->translate('age'));
		Assert::same('city_name', $translator->translate('city_name'));

		Assert::same('cs', $translator->getIso());

		$translator->setIso('fr');

		Assert::same('nom', $translator->translate('name'));
		Assert::same('âge', $translator->translate('age'));
		Assert::same('city_name', $translator->translate('city_name'));

		Assert::same('fr', $translator->getIso());

		$translator->setIso('sk');

		Assert::same('name', $translator->translate('name'));
		Assert::same('age', $translator->translate('age'));
		Assert::same('city_name', $translator->translate('city_name'));

		Assert::same('sk', $translator->getIso());
	}


	public function testDefaultLanguage(): void
	{
		$translator = new TranslatorSettings($settings = Mockery::mock(Settings::class), $language = Mockery::mock(Language::class));
		$language->shouldReceive('get')->withNoArgs()->once()->andReturn('en');
		$settings->shouldReceive('load')->with('translates:en')->once()->andReturn(['name' => 'OK']);

		Assert::same('en', $translator->getIso());

		Assert::same('OK', $translator->translate('name'));
	}
}

(new TranslatorSettingsTest())->run();
