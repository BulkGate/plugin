<?php declare(strict_types=1);

namespace BulkGate\Plugin\Localization;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\{Strict, Settings\Settings};
use function is_array, is_string;

class TranslatorSettings implements Translator
{
	use Strict;

	private Settings $settings;

	private ?string $iso = null;

	/**
	 * @var array<string, string>
	 */
	private array $translates;

	public function __construct(Settings $settings)
	{
		$this->settings = $settings;
	}


	public function translate(string $message, ...$parameters): string
	{
		$this->init();

		return $this->translates[$message] ?? $message;
	}


	public function getIso(): string
	{
		return $this->iso ?? 'en';
	}


	public function setIso(string $iso): void
	{
		$this->settings->set('main:language', $iso, ['type' => 'string']);

		$this->init($iso);
	}


	private function init(?string $iso = null): void
	{
		if (($iso !== null && $this->iso !== $iso) || !isset($this->translates))
		{
			$iso ??= $this->settings->load('main:language') ?? 'en';

			$this->iso = is_string($iso) ? $iso : 'en';

			$translates = $this->settings->load("translates:$this->iso");

			$this->translates = is_array($translates) ? $translates : [];
		}
	}
}
