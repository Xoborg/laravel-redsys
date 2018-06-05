<?php

namespace Xoborg\LaravelRedsys;

use Illuminate\Support\ServiceProvider;

class LaravelRedsysServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/config/redsys.php' => config_path('redsys.php'),
		]);
	}

	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__.'/config/redsys.php', 'redsys'
		);
	}
}