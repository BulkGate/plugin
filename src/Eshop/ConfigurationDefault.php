<?php declare(strict_types=1);

namespace BulkGate\Plugin\Eshop;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\Strict;

class ConfigurationDefault implements Configuration
{
	use Strict;

	private string $url;
	private string $product;

	private string $version;

	public function __construct(string $url, string $product, string $version)
	{
		$this->url = $url;
		$this->product = $product;
		$this->version = $version;
	}


	public function url(): string
	{
		return $this->url;
	}


	public function product(): string
	{
		return $this->product;
	}


	public function version(): string
	{
		return $this->version;
	}
}
