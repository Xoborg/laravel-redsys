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

		if (!class_exists('CreatePagosRedsysTable') && !class_exists('CreateNotificacionesOnlineRedsysTable')) {
			$this->publishes([
				__DIR__.'/database/migrations/create_pagos_redsys_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_pagos_redsys_table.php'),
				__DIR__.'/database/migrations/create_notificaciones_online_redsys_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time() + 1).'_create_notificaciones_online_redsys_table.php'),
			], 'migrations');
		}

		$this->publishes([
			__DIR__.'/resources/views' => resource_path('views/vendor/laravel-redsys'),
		], 'views');
	}

	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__.'/config/redsys.php', 'redsys'
		);
	}
}