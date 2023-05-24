<?php declare(strict_types=1);

interface Connection
{
}


class ConnectionTest implements Connection
{
}


class ConnectionProduction implements Connection
{
}


class Service
{
	public Connection $connection;

	public Connection $production;

	public string $name;


	public function __construct(Connection $connection, ConnectionProduction $connection_production, string $name)
	{
		$this->connection = $connection;
		$this->production = $connection_production;
		$this->name = $name;
	}
}


class TestClassEntity
{
	public string $name;

	public function __construct(string $name = 'test')
	{
		$this->name = $name;
	}
}
