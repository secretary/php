<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */
class Test extends \Symfony\Component\HttpKernel\Kernel
{
	use MicroKernelTrait;
	const CONFIG_EXTS = '.{php,xml,yaml,yml}';

	/**
	 * Returns an array of bundles to register.
	 *
	 * @return iterable|\Symfony\Component\HttpKernel\Bundle\BundleInterface An iterable of bundle instances
	 */
	public function registerBundles()
	{
		yield new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();
		yield new \Secretary\Bundle\SecretaryBundle\SecretaryBundle();
	}

	/**
	 * Add or import routes into your application.
	 *
	 *     $routes->import('config/routing.yml');
	 *     $routes->add('/admin', 'App\Controller\AdminController::dashboard', 'admin_dashboard');
	 *
	 * @param \Symfony\Component\Routing\RouteCollectionBuilder $routes
	 */
	protected function configureRoutes(\Symfony\Component\Routing\RouteCollectionBuilder $routes)
	{
	}

	/**
	 * {@inheritDc}
	 * @param ContainerBuilder $c
	 * @param LoaderInterface  $loader
	 *
	 * @throws Exception
	 */
	protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
	{
		$loader->load(__DIR__.'/services'.self::CONFIG_EXTS, 'glob');
	}
}

$k = new Test('dev', true);
$k->boot();
var_dump($k->getContainer()->getParameter('foo'));