<?php

namespace Xoborg\LaravelRedsys;

use Illuminate\Support\ServiceProvider;

class LaravelRedsysServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/config/redsys.php' => config_path('redsys.php'),
		], 'config');

		if (! class_exists('CreatePagosRedsysTable')) {
			$this->publishes([
				__DIR__.'/database/migrations/create_pagos_redsys_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_pagos_redsys_table.php'),
			], 'migrations');
		}
	}

	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__.'/config/redsys.php', 'redsys'
		);
	}
}