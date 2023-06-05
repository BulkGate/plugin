<?php declare(strict_types=1);

interface Connection
{
	public function test(): string;
}


class ConnectionTest implements Connection
{
	public function test(): string
	{
		return 'test';
	}
}


class ConnectionProduction implements Connection
{
	public function test(): string
	{
		return 'production';
	}
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
