<?php
declare(strict_types=1);

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2019
 * @license   http://opensource.org/licenses/MIT
 */

namespace Secretary;

use Secretary\Adapter\AdapterInterface;
use Secretary\Adapter\Secret;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Manager
 *
 * @package Secretary
 */
class Manager
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Manager constructor.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param string $key
     * @param array  $options
     *
     * @return Secret
     */
    public function getSecret(string $key, ?array $options = []): Secret
    {
        $resolver = new OptionsResolver();
        $this->adapter->configureSharedOptions($resolver);
        $this->adapter->configureGetSecretOptions($resolver);


        return $this->adapter->getSecret($key, $resolver->resolve($options));
    }

    /**
     * @param string       $key
     * @param string|array $value
     * @param array        $options
     *
     * @return void
     */
    public function putSecret(string $key, $value, ?array $options = []): void
    {
        $resolver = new OptionsResolver();
        $this->adapter->configureSharedOptions($resolver);
        $this->adapter->configurePutSecretOptions($resolver);

        $this->adapter->putSecret($key, $value, $resolver->resolve($options));
    }

    /**
     * @param string $key
     * @param array  $options
     *
     * @return void
     */
    public function deleteSecret(string $key, ?array $options = []): void
    {
        $resolver = new OptionsResolver();
        $this->adapter->configureSharedOptions($resolver);
        $this->adapter->configureDeleteSecretOptions($resolver);

        $this->adapter->deleteSecret($key, $resolver->resolve($options));
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }
}
