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
		//$this->app['router'] = $this->app->share(function() { return new CustomRouter($this->app); });

		$this->app['router'] = $this->app->share(function($app)
		{
			$router = new CustomRouter($app);

			// If the current application environment is "testing", we will disable the
			// routing filters, since they can be tested independently of the routes
			// and just get in the way of our typical controller testing concerns.
			/*if ($app['env'] == 'testing')
			{
				$router->disableFilters();
			}*/

			return $router;
		});
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