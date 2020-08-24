<?php

namespace Xoborg\LaravelRedsys\Tests;

use \Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
	protected function setUp(): void
	{
		parent::setUp();
		$this->setUpDatabase();
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		// Setup default database to use sqlite :memory:
		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', [
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		]);
	}

	/**
	 * @param \Illuminate\Foundation\Application $app
	 * @return array
	 */
	protected function getPackageProviders($app)
	{
		return [
			\Xoborg\LaravelRedsys\LaravelRedsysServiceProvider::class
		];
	}

	public function setUpDatabase()
	{
		include_once __DIR__.'/../src/database/migrations/create_pagos_redsys_table.php.stub';
		(new \CreatePagosRedsysTable())->up();

		include_once __DIR__.'/../src/database/migrations/create_notificaciones_online_redsys_table.php.stub';
		(new \CreateNotificacionesOnlineRedsysTable())->up();
	}
}
