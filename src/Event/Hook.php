<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\{IO\Request, IO\Url, Strict, IO\Connection};

class Hook
{
	use Strict;

	private string $version;

	private Connection $connection;

	private Url $url;

	public function __construct(string $version, Connection $connection, Url $url)
	{
		$this->version = $version;
		$this->connection = $connection;
		$this->url = $url;
	}


	/**
	 * @param string $category order, customer, cart, product, return
	 * @param array<string, scalar|null> $variables
	 */
	public function dispatch(string $category, string $endpoint, array $variables): void
	{
		$this->connection->run(
			new Request(
				$this->url->get("api/$this->version/eshop/$category/$endpoint"),
				['variables' => $variables]
			)
		);
	}
}
