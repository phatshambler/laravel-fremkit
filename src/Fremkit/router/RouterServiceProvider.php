<?php namespace Fremkit\Router;

use Illuminate\Support\ServiceProvider;

class RouterServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['router'] = $this->app->share(function() { return new CustomRouter($this->app); });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('router');
	}

}