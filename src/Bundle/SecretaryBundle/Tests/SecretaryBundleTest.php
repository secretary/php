<?php

declare(strict_types=1);

/*
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   https://opensource.org/licenses/MIT
 */

namespace Secretary\Tests;

use Secretary\Bundle\SecretaryBundle\SecretaryBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class SecretaryBundleTest extends Kernel
{
    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * Returns an array of bundles to register.
     *
     * @return iterable|\Symfony\Component\HttpKernel\Bundle\BundleInterface An iterable of bundle instances
     */
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new SecretaryBundle();
    }

    /**
     * Add or import routes into your application.
     *
     *     $routes->import('config/routing.yml');
     *     $routes->add('/admin', 'App\Controller\AdminController::dashboard', 'admin_dashboard');
     *
     */
    protected function configureRoutes(Symfony\Component\Routing\RouteCollectionBuilder $routes)
    {
    }

    /**
     * {@inheritDc}.
     *
     * @throws Exception
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/services'.self::CONFIG_EXTS, 'glob');
    }
}

$k = new SecretaryBundleTest('dev', true);
$k->boot();
var_dump($k->getContainer()->getParameter('foo'));
