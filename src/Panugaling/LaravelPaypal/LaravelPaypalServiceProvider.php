<?php namespace Panugaling\LaravelPaypal;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class LaravelPaypalServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['Paypal'] = $this->app->share(function ($app) {
			return new Paypal;
		});
	}

	public function boot() {
		$this->package('panugaling/laravel-paypal');
		
		AliasLoader::getInstance()->alias('Paypal', 'Panugaling\LaravelPaypal\Paypal');
	}
	
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
